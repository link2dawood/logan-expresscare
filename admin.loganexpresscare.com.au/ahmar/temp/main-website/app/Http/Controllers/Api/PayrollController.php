<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Payroll\IndexRequest;
use App\Http\Requests\Api\Payroll\StoreRequest;
use App\Http\Requests\Api\Payroll\UpdateRequest;
use App\Http\Requests\Api\Payroll\DeleteRequest;
use App\Http\Requests\Api\Payroll\PayrollGenerateRequest;
use App\Http\Requests\Api\Payroll\UpdateStatusRequest;
use App\Http\Requests\Api\Payroll\PayrollRegenerateRequest;
use App\Models\Payroll;
use Examyou\RestAPI\ApiResponse;
use App\Classes\CommonHrm;
use App\Classes\Payrolls;
use App\Models\PayrollComponent;

class PayrollController extends ApiBaseController
{
    protected $model = Payroll::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $payrollGenerateRequest = PayrollGenerateRequest::class;
    protected $payrollRegenerateRequest = PayrollRegenerateRequest::class;
    protected $updateStatusRequest = UpdateStatusRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();
        // year Filters
        if ($request->has('year') && $request->year != "") {
            $payrollYear = $request->year;
            $query = $query->where('payrolls.year', $payrollYear);
        }

        // month Filters
        if ($request->has('month') && $request->month != "") {
            $payrollMonth = $request->month;
            $query = $query->where('payrolls.month', $payrollMonth);
        }

        if ($loggedUser->ability('admin', 'payrolls_view')) {
            $query = $this->applyVisibility($query, 'payrolls');

            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('payrolls.user_id', $userId);
            }
        } else {
            $query = $query->where('payrolls.user_id', $loggedUser->id);
        }

        return  $query;
    }

    public function storing($payroll)
    {
        $loggedUser = user();

        $payroll->created_by = $loggedUser->id;

        return $payroll;
    }

    public function updated($payroll)
    {
        $request = request();
        // this is call when status change like paid,generated
        Payrolls::updatePayrollStatus($request->account_id, $request->payrolls, $request->payroll_status, $request->payment_date);

        //update net salary of user
        $payroll->basic_salary = $request->basic_salary;
        $payroll->expense_amount = $request->expense_amount;
        $payroll->net_salary = $request->net_salary;
        $payroll->save();


        $customEarnings = $request->custom_earnings ?? [];
        $customDeductions = $request->custom_deductions ?? [];
        $additionalEarnings = $request->additional_earnings ?? [];
        $predefinedEarnings = $request->predefined_earnings ?? [];
        $predefinedDeductions = $request->predefined_deductions ?? [];


        $allComponents = array_merge(
            array_map(fn($item) => array_merge($item, ['type' => 'custom_earning']), $customEarnings),
            array_map(fn($item) => array_merge($item, ['type' => 'custom_deduction']), $customDeductions),
            array_map(fn($item) => array_merge($item, ['type' => 'additional_earning']), $additionalEarnings),
            array_map(fn($item) => array_merge($item, ['type' => $item['type']]), $predefinedEarnings),
            array_map(fn($item) => array_merge($item, ['type' => $item['type']]), $predefinedDeductions)
        );


        $existingComponents = PayrollComponent::where('payroll_id', $payroll->id)
            ->where('user_id', $payroll->user_id)
            ->whereIn('type', ['custom_earning', 'custom_deduction', 'additional_earning', 'earnings', 'deductions', 'pre_payments', 'expenses'])
            ->get();

        $xidInRequest = collect($allComponents)
            ->pluck('xid')
            ->filter()
            ->map(fn($xid) => $this->getIdFromHash($xid))
            ->toArray();

        foreach ($allComponents as $component) {
            $payrollComponent = null;

            if (isset($component['salary_component_id'])) {
                $payrollComponent = PayrollComponent::where('salary_component_id', $this->getIdFromHash($component['salary_component_id']))
                    ->where('payroll_id', $payroll->id)
                    ->where('user_id', $payroll->user_id)
                    ->first();
            }

            if (!$payrollComponent && isset($component['xid']) && !empty($component['xid'])) {
                $payrollComponent = PayrollComponent::find($this->getIdFromHash($component['xid']));
            }

            if ($payrollComponent) {
                $payrollComponent->name = $component['name'];
                $payrollComponent->amount =  floatval(
                    $component['monthly']
                );
                $payrollComponent->type = $component['type'];
                $payrollComponent->monthly_value =  $component['monthly_value'] ??  $component['monthly'];
                $payrollComponent->value_type = in_array($component['value_type'], ['ctc_percent', 'basic_percent', 'fixed'])
                    ? $component['value_type']
                    : 'variable';
                $payrollComponent->is_earning = in_array($component['type'], ['deductions', 'custom_deduction']) ? 0 : 1;
                $payrollComponent->save();
            } else {
                PayrollComponent::create([
                    'payroll_id' => $payroll->id,
                    'user_id' => $payroll->user_id,
                    'name' => $component['name'],
                    'amount' =>
                    floatval(
                        $component['monthly']
                    ),

                    'monthly_value' => $component['monthly_value'] ??  $component['monthly'],
                    'type' => $component['type'],
                    'value_type' => "variable",
                    'is_earning' => in_array($component['type'], ['deductions', 'custom_deduction']) ? 0 : 1,
                    'salary_component_id' => $component['salary_component_id'] ?? null,
                ]);
            }
        }

        foreach ($existingComponents as $existingComponent) {
            if (
                !in_array($existingComponent->id, $xidInRequest) &&
                in_array($existingComponent->type, ['custom_earning', 'custom_deduction', 'additional_earning'])
            ) {
                $existingComponent->delete();
            }
        }
    }

    public function payrollGenerate(PayrollGenerateRequest $payrollGenerateRequest)
    {
        $user = user();
        $company = company();
        Payrolls::payrollGenerateRegenerate($payrollGenerateRequest, $user, $company);
        return ApiResponse::make('Generate Successfully', []);
    }

    public function destroyed(Payroll $payroll)
    {
        if ($payroll->account_id) {
            CommonHrm::updateAccountAmount($payroll->account_id);
        }
    }

    public function updateStatus(UpdateStatusRequest $updateStatusRequest)
    {
        // this is call when status change like paid,generated
        Payrolls::updatePayrollStatus($updateStatusRequest->account_id, $updateStatusRequest->payrolls, $updateStatusRequest->payroll_status, $updateStatusRequest->payment_date);
    }
}

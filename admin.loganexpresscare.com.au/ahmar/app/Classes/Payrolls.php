<?php

namespace App\Classes;

use App\Models\BasicSalaryDetails;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PrePayment;
use App\Models\SalaryComponent;
use App\Models\SalaryGroupComponent;
use App\Models\SalaryGroupUser;
use App\Models\StaffMember;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

class Payrolls
{
    public static function updateUserSalary($userId, $annualCTC)
    {
        $user = StaffMember::find($userId);
        $calculationType = $user->calculation_type;
        $ctcValue = (float)$user->ctc_value;
        if ($ctcValue == 0 || $annualCTC == 0) {
            $monthlySalary = 0;
        } else {
            if ($ctcValue == 0 || $ctcValue === null) {
                $monthlySalary = 0;
            } else {
                $monthlySalary = $calculationType === 'fixed'
                    ? $ctcValue
                    : ($annualCTC * $ctcValue) / 100 / 12;
            }
        }

        $annualAmount = $monthlySalary * 12;
        $monthlyCtc = $annualCTC / 12;
        $earnings = 0;
        $deductions = 0;

        $basicSalaryDetails = $user->basicSalaryDetails ? $user->basicSalaryDetails : [];
        foreach ($basicSalaryDetails as $group) {
            $salaryComponents = $group->salaryComponent ? $group->salaryComponent : [];
            foreach ($salaryComponents as $component) {
                $amount = 0.0;

                switch ($component->value_type) {
                    case 'fixed':
                        $amount = (float) $component->monthly;
                        break;
                    case 'variable':
                        $amount = (float) $group['monthly'];
                        break;

                    case 'basic_percent':
                        $amount = ($monthlySalary * (float) $component->monthly) / 100;
                        break;

                    case 'ctc_percent':
                        if ($ctcValue != 0) {
                            $amount = ($monthlySalary * (float) $component->monthly) / $ctcValue;
                        } else {
                            $amount = 0;
                        }
                        break;

                    default:
                        $amount = 0.0;
                        break;
                }

                if ($component->type === 'earnings') {
                    $earnings += (float) $amount;
                } elseif ($component->type === 'deductions') {
                    $deductions += (float) $amount;
                }
            }
        }
        $specialAllowance = number_format(($monthlyCtc - $monthlySalary - $earnings), 2, '.', '');
        $netSalary = number_format(
            ($monthlySalary + $specialAllowance + $earnings - $deductions),
            2,
            '.',
            ''
        );
        $user->update([
            'basic_salary' => $monthlySalary,
            'monthly_amount' => $netSalary,
            'annual_amount' => $annualAmount,
            'annual_ctc' => $annualCTC,
            'special_allowances' => $specialAllowance,
            'net_salary'
            => $netSalary
        ]);
    }

    // this is use for change payroll status generate to paid
    public static function updatePayrollStatus($accountReqId, $payrolls, $payrollStatus, $paymentDate)
    {
        $loggedUser = user();
        $accountId = Common::getIdFromHash($accountReqId);

        if (!$loggedUser->ability('admin', 'payrolls_edit')) {
            throw new ApiException("Not have valid permission");
        }
        if ($payrolls) {
            $paymentStatus = $payrollStatus;

            $payrollIds = [];

            foreach ($payrolls as $value) {
                $payrollIds[] = Common::getIdFromHash($value);
            }

            foreach ($payrollIds as $payrollId) {
                $payroll = Payroll::find($payrollId);
                if ($payroll) {
                    $payroll->status = $paymentStatus;

                    if ($paymentStatus == 'paid') {
                        $payroll->payment_date = $paymentDate;
                        $payroll->account_id = $accountId;
                    } else {
                        $payroll->payment_date = null;
                    }

                    $payroll->save();
                    if ($paymentStatus == 'paid') {
                        CommonHrm::insertAccountEntries($payroll->account_id, null, "payroll", $payroll->payment_date, $payroll->id, $payroll->net_salary);
                        CommonHrm::updateAccountAmount($payroll->account_id);

                        // Notifying to User
                        Notify::send('employee_payroll_paid', $payroll);
                    }
                }
            }
        }

        return ApiResponse::make('Status Updated Successfully', []);
    }


    // This function is use for update or create basic salary from basic salary and employee modules
    public static function updateEmployeeSalary($id, $calculationType, $basicSalary, $monthlyAmount, $annualAmount, $annualCtc, $ctcValue, $netSalary, $specialAllowances, $salaryComponents, $salaryGroupId)
    {
        $user = StaffMember::find($id);

        // Update user basic salary details
        $user->basic_salary = $basicSalary;
        $user->monthly_amount
            = $monthlyAmount;
        $user->annual_amount = $annualAmount;
        $user->annual_ctc = $annualCtc;
        $user->ctc_value = $ctcValue;
        $user->net_salary = $netSalary;
        $user->special_allowances = $specialAllowances;
        $user->calculation_type = $calculationType;
        $user->save();

        self::changeGroup($id, $user->annual_ctc, $salaryGroupId, $salaryComponents);
    }

    public static function changeGroup($userId, $annualCtc, $groupId, $salaryComponents)
    {
        $user = StaffMember::find($userId);
        $user->salary_group_id = $groupId;
        $user->save();

        SalaryGroupUser::updateOrCreate(
            ['user_id' => $userId],
            ['salary_group_id' => $groupId]
        );

        $salaryGroupComponents = SalaryGroupComponent::where('salary_group_id', $groupId)
            ->with('salaryGroup.salaryGroupComponents')
            ->get();

        BasicSalaryDetails::where('user_id', $userId)->delete();

        $salaryDetails = [];

        $convertedSalaryComponents = [];

        if (!empty($salaryComponents) && is_array($salaryComponents)) {
            $convertedSalaryComponents = array_map(function ($component) {
                return [
                    'id' => Common::getIdFromHash($component['id']),
                    'type' => $component['type'],
                    'value_type' => $component['value_type'],
                    'monthly_value' => $component['monthly_value'],
                ];
            }, $salaryComponents);
        }

        foreach ($salaryGroupComponents as $component) {

            $matchingComponent = collect($convertedSalaryComponents)->firstWhere('id', $component->salary_component_id);

            if ($matchingComponent) {
                $salaryDetails[] = [
                    'user_id' => $userId,
                    'salary_component_id' => $component->salary_component_id,
                    'type' => $matchingComponent['type'],
                    'value_type' => $matchingComponent['value_type'],
                    'monthly_value' => $matchingComponent['monthly_value']
                ];
            }
        }

        if (!empty($salaryDetails)) {
            foreach ($salaryDetails as $salaryDetail) {

                $basicSalaryDetails = new BasicSalaryDetails();
                $basicSalaryDetails->user_id = $salaryDetail['user_id'];
                $basicSalaryDetails->salary_component_id = $salaryDetail['salary_component_id'];
                $basicSalaryDetails->type = $salaryDetail['type'];
                $basicSalaryDetails->value_type = $salaryDetail['value_type'];
                $basicSalaryDetails->monthly =
                    $salaryDetail['monthly_value'];
                $basicSalaryDetails->save();
            }
        }

        self::updateUserSalary($userId, $annualCtc);
    }

    public static function payrollGenerateRegenerate($payrollGenerateRequest, $user, $company)
    {
        if ($payrollGenerateRequest->has('year') && $payrollGenerateRequest->has('month') && $payrollGenerateRequest->month > 0) {
            $year = (int) $payrollGenerateRequest->year;
            $month = (int) $payrollGenerateRequest->month;

            $allUsers = StaffMember::select('id', 'basic_salary', 'salary_group_id', 'ctc_value', 'annual_ctc', 'calculation_type')
                ->where('status', 'active')
                ->whereNotNull('ctc_value')
                ->with(['salaryGroup.salaryGroupComponents.salaryComponent', 'basicSalaryDetails.salaryComponent']);

            if ($payrollGenerateRequest->has('users')) {
                $userIds = Common::getIdArrayFromHash($payrollGenerateRequest->users);
                $allUsers = $allUsers->whereIn('id', $userIds);
            } else {
                $existingPayrolls = Payroll::where('month', $month)
                    ->where('year', $year)
                    ->pluck('user_id')
                    ->toArray();

                $allUsers = $allUsers->whereNotIn('id', $existingPayrolls);
            }

            $allUsers = $allUsers->get();

            if ($allUsers->isEmpty()) {
                return;
            }

            foreach ($allUsers as $allUser) {
                $payroll = Payroll::where('month', $month)
                    ->where('year', $year)
                    ->where('user_id', $allUser->id)
                    ->first();

                if ($payroll && in_array($payroll->status, ['paid'])) {
                    continue;
                }

                if (!$payroll) {
                    $payroll = new Payroll();
                    $payroll->created_by = $user->id;
                }

                // Delete existing components
                PayrollComponent::where('payroll_id', $payroll->id)
                    ->where(function ($query) {
                        $query->whereIn('type', [
                            'custom_earning',
                            'custom_deduction',
                            'additional_earning',
                            'expenses',
                            'pre_payments',
                            'earnings',
                            'deductions',
                        ]);
                    })
                    ->delete();

                // Recalculate basic salary
                $basicSalary = $allUser->basic_salary ?? 0;

                $resultData = CommonHrm::getMonthYearAttendanceDetails($allUser->id, $month, $year);
                $holidayCount = $resultData['holiday_count'];
                $paidLeaveCount = $resultData['total_paid_leaves'];
                $totalDaysInMonth = $resultData['total_days'];
                $workingDays = $resultData['working_days'];
                $presentDays = $resultData['present_days'];
                $totalUnpaidLeaves = $resultData['total_unpaid_leaves'];

                $payroll->user_id = $allUser->id;
                $payroll->year = $year;
                $payroll->month = $month;
                $payroll->total_days = $totalDaysInMonth;
                $payroll->basic_salary = round($basicSalary, 2);
                $payroll->working_days = $resultData['working_days'];
                $payroll->present_days = $resultData['present_days'];
                $payroll->total_office_time = $resultData['total_office_time'];
                $payroll->total_worked_time = $resultData['clock_in_duration'];
                $payroll->half_days = $resultData['half_days'];
                $payroll->late_days = $resultData['total_late_days'];
                $payroll->paid_leaves = $paidLeaveCount;
                $payroll->unpaid_leaves = $resultData['total_unpaid_leaves'];
                $payroll->holiday_count = $holidayCount;
                $payroll->salary_amount = 0;
                $payroll->net_salary = 0;
                $payroll->status = "generated";
                $payroll->updated_by = $user->id;
                $payroll->payment_date = null;
                $payroll->company_id = $company->id;
                $payroll->save();

                //insert salary component according to salary group which is assign to users
                $earnings = 0.0;
                $deductions = 0.0;

                if ($allUser->basicSalaryDetails->isNotEmpty()) {
                    foreach ($allUser->basicSalaryDetails as $basicSalaryDetail) {
                        $salaryComponent = SalaryComponent::find($basicSalaryDetail->salary_component_id);

                        $basicSalary = $allUser->basic_salary;
                        $annualCTC = (float) $allUser->annual_ctc;
                        $ctcAmountMonthly = (float)  $annualCTC / 12;
                        $monthlyCtc =
                            ($ctcAmountMonthly * $presentDays) / $totalDaysInMonth;

                        $unpaidDaysPayment =  ($ctcAmountMonthly * $totalUnpaidLeaves) / $totalDaysInMonth;

                        $payrollComponent = new PayrollComponent();
                        $payrollComponent->user_id = $allUser->id;
                        $payrollComponent->payroll_id = $payroll->id;
                        $payrollComponent->salary_component_id = $basicSalaryDetail->salary_component_id;
                        $payrollComponent->name = $salaryComponent['name'];
                        $payrollComponent->value_type = $basicSalaryDetail->value_type;
                        $payrollComponent->type = $basicSalaryDetail->type;
                        $payrollComponent->is_earning = $basicSalaryDetail->type == 'deductions' ? 0 : 1;
                        $payrollComponent->company_id = $company->id;
                        $payrollComponent->amount = in_array($basicSalaryDetail->value_type, ['basic_percent', 'ctc_percent'])
                            ? $salaryComponent['monthly']
                            : $basicSalaryDetail['monthly'];

                        $payrollComponent->save();

                        $amount = 0.0;
                        switch ($payrollComponent->value_type) {
                            case 'fixed':
                                $amount = (float) $payrollComponent->amount;
                                break;
                            case 'variable':
                                $amount = (float) $payrollComponent['amount'];
                                break;
                            case 'basic_percent':
                                $amount = ($basicSalary * (float) $payrollComponent->amount) / 100;
                                break;
                            case 'ctc_percent':
                                $amount = ($monthlyCtc * (float) $payrollComponent->amount) / 100;
                                break;
                            default:
                                $amount = 0.0;
                                break;
                        }
                        $payrollComponent->monthly_value = round($amount, 2);
                        $payrollComponent->save();

                        if (strtolower(trim($payrollComponent->type)) === 'earnings') {
                            $earnings += $amount;
                        } elseif (strtolower(trim($payrollComponent->type)) === 'deductions') {
                            $deductions += $amount;
                        }
                    }
                }
                $ctcAmountMonthly = 0;

                if (isset($allUser->annual_ctc) && $allUser->annual_ctc > 0) {
                    $annualCTC = (float) $allUser->annual_ctc;
                    $ctcAmountMonthly = $annualCTC / 12;
                }

                $unpaidDaysPayment = 0;

                if ($totalDaysInMonth > 0) {
                    $unpaidDaysPayment = ($ctcAmountMonthly * $totalUnpaidLeaves) / $totalDaysInMonth;
                }

                if ($unpaidDaysPayment != 0) {
                    $unpaidDaysComponent = new PayrollComponent();
                    $unpaidDaysComponent->user_id = $allUser->id;
                    $unpaidDaysComponent->payroll_id = $payroll->id;
                    $unpaidDaysComponent->name = "Leave Deduction";
                    $unpaidDaysComponent->value_type = 'fixed';
                    $unpaidDaysComponent->type = 'deductions';
                    $unpaidDaysComponent->is_earning = 0;
                    $unpaidDaysComponent->company_id = $company->id;
                    $unpaidDaysComponent->amount = round($unpaidDaysPayment, 2);
                    $unpaidDaysComponent->monthly_value
                        = round($unpaidDaysPayment, 2);
                    $unpaidDaysComponent->save();
                }


                $specialAllowance = $basicSalary - $earnings;

                $totalPrePaymentAmount = 0;
                $totalExpenseAmount = 0;


                // Getting all Pre payments
                $allPrepayments = PrePayment::where('user_id', $allUser->id)
                    ->where('payroll_month', $month)
                    ->where('payroll_year', $year)
                    ->get();

                foreach ($allPrepayments as $allPrepayment) {
                    $newPrePaymentComponent = new PayrollComponent();
                    $newPrePaymentComponent->payroll_id = $payroll->id;
                    $newPrePaymentComponent->pre_payment_id = $allPrepayment->id;
                    $newPrePaymentComponent->user_id = $allUser->id;
                    $newPrePaymentComponent->name = "Pre Payments";
                    $newPrePaymentComponent->amount = $allPrepayment->amount;
                    $newPrePaymentComponent->monthly_value = $allPrepayment->amount;
                    $newPrePaymentComponent->is_earning = 0;
                    $newPrePaymentComponent->type = 'pre_payments';
                    $newPrePaymentComponent->company_id = $company->id;
                    $newPrePaymentComponent->save();

                    $totalPrePaymentAmount += $allPrepayment->amount;
                }

                // Getting all Expenses
                $allExpenses = Expense::where('user_id', $allUser->id)
                    ->where('payment_status', '0')
                    ->where('status', 'approved')
                    ->where('payroll_month', $month)
                    ->where('payroll_year', $year)
                    ->get();

                foreach ($allExpenses as $allExpense) {
                    $newExpenseComponent = new PayrollComponent();
                    $newExpenseComponent->payroll_id = $payroll->id;
                    $newExpenseComponent->expense_id = $allExpense->id;
                    $newExpenseComponent->user_id = $allUser->id;
                    $newExpenseComponent->name = "Expense Claim";
                    $newExpenseComponent->amount = $allExpense->amount;
                    $newExpenseComponent->monthly_value = $allExpense->amount;
                    $newExpenseComponent->is_earning = 1;
                    $newExpenseComponent->type = 'expenses';
                    $newExpenseComponent->company_id = $company->id;
                    $newExpenseComponent->save();

                    $totalExpenseAmount += $allExpense->amount;
                }

                $payroll->pre_payment_amount = $totalPrePaymentAmount;
                $payroll->expense_amount = $totalExpenseAmount;

                $payroll->net_salary =   round($ctcAmountMonthly - $basicSalary - $totalPrePaymentAmount + $totalExpenseAmount
                    + $earnings - $deductions + $specialAllowance - $unpaidDaysPayment, 2);

                $payroll->salary_amount
                    = round($ctcAmountMonthly - $basicSalary - $totalPrePaymentAmount + $totalExpenseAmount
                        + $earnings - $deductions + $specialAllowance - $unpaidDaysPayment, 2);

                // This is use for update account balance on payroll generate and regenerate
                if ($payroll->account_id) {
                    DB::table('account_entries')
                        ->where('payroll_id', $payroll->id)
                        ->where('account_id', $payroll->account_id)
                        ->delete();

                    CommonHrm::updateAccountAmount($payroll->account_id);
                }
                $payroll->save();
            }
        }
    }
}

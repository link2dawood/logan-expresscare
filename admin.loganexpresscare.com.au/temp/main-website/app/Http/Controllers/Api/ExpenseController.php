<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Expense\IndexRequest;
use App\Http\Requests\Api\Expense\StoreRequest;
use App\Http\Requests\Api\Expense\UpdateRequest;
use App\Http\Requests\Api\Expense\DeleteRequest;
use App\Http\Requests\Api\Expense\UpdateStatusRequest;
use App\Http\Requests\Api\Expense\UpdateDashStatusRequest;
use App\Models\Expense;
use App\Models\AccountEntry;
use App\Models\Payroll;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Examyou\RestAPI\ApiResponse;

class ExpenseController extends ApiBaseController
{
    public $oldAccountId;
    protected $model = Expense::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $updateStatusRequest = UpdateStatusRequest::class;
    protected $updateDashStatusRequest = UpdateDashStatusRequest::class;

    public function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();
        $expenseType = $request->expense_type;

        // status Filters
        if ($request->has('status') && $request->status != '') {
            $query = $query->where('expenses.status', $request->status);
        }

        if ($request->has('dates') && $request->dates != '') {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('expenses.date_time >= ?', [$startDate])
                ->whereRaw('expenses.date_time <= ?', [$endDate]);
        }

        if ($request->has('expense_type') && $request->expense_type != '') {
            $query = $query->where('expenses.expense_type', '=', $expenseType);

            if ($expenseType == 'employee_claims') {
                $query = $query->whereHas('expenseCategory', function ($q) {
                    $q->whereIn('expense_for', ['employee_specific', 'all']);
                });
            } elseif ($expenseType == 'company_claims') {
                $query = $query->whereHas('expenseCategory', function ($q) {
                    $q->whereIn('expense_for', ['company_specific', 'all']);
                });
            }

            if ($loggedUser->ability('admin', 'expenses_view') && $request->expense_type == 'employee_claims') {
                $query = $this->applyVisibility($query, 'expenses');
            }
        } else {
            $query = $query->where('expenses.user_id', $loggedUser->id);
        }

        return $query;
    }

    public function updateExpenseValues($expense, $status = 'approved')
    {
        if ($status == 'rejected') {
            $expense->payment_status = 0;
            $expense->payroll_month = null;
            $expense->payroll_year = null;
            $expense->payee_id = null;
            $expense->account_id = null;
            $expense->payment_date = null;
        } else {
            if ($expense->payment_status == 1) {
                $expense->payroll_month = null;
                $expense->payroll_year = null;
            } else {
                $expense->payee_id = null;
                $expense->account_id = null;
                $expense->payment_date = null;
            }
        }

        // Expense always will be approved
        $expense->status = $status;

        return $expense;
    }

    public function storing(Expense $expense)
    {
        if ($expense->payment_status == '0' && $expense->status == 'approved' && $expense->expense_type == 'employee_claims') {
            $userId = $expense->user_id;
            $month = $expense->payroll_month;
            $year = $expense->payroll_year;

            $payroll = Payroll::where('payrolls.user_id', $userId)
                ->where('payrolls.month', $month)
                ->where('payrolls.year', $year)
                ->count();

            if ($payroll > 0) {
                throw new ApiException("Payroll already generated for this employee for month " . $month . '-' . $year);
            }
        }

        return $this->updateExpenseValues($expense);
    }

    public function stored(Expense $expense)
    {
        if ($expense->payment_status == 1 && $expense->status == 'approved') {
            $this->updatingStatusAccountEntries($expense);
        }

        // Notifying to User
        if ($expense->status == 'approved') {
            Notify::send('employee_expense_approve', $expense);
        } else if ($expense->status == 'rejected') {
            Notify::send('employee_expense_reject', $expense);
        }
    }

    public function updating(Expense $expense)
    {
        $request = request();

        if ($expense->expense_type != $expense->getOriginal('expense_type')) {
            throw new ApiException("You can not change the expense type");
        }

        $this->oldAccountId = $expense->getOriginal('account_id');

        return $this->updateExpenseValues($expense, $request->status);
    }


    public function updated(Expense $expense)
    {
        if ($expense->account_id != null) {
            $this->updatingStatusAccountEntries($expense);
        } else {
            AccountEntry::where('expense_id', $expense->id)->delete();
        }

        if ($this->oldAccountId != null) {
            CommonHrm::updateAccountAmount($this->oldAccountId);
        }
    }

    public function destroyed(Expense $expense)
    {
        CommonHrm::updateAccountAmount($expense->account_id);
    }

    public function changeStatus(UpdateStatusRequest $request, $xid)
    {
        $id = $this->getIdFromHash($xid);
        $expense = Expense::find($id);

        if ($expense->status == 'approved' || $expense->status == 'rejected') {
            throw new ApiException("Expense already apporved or rejected");
        }

        $expense->payment_status = $request->payment_status;
        $expense->status = $request->status;

        if ($expense->payment_status == 1) {
            $expense->payment_date = $request->payment_date;
            $expense->account_id = $request->account_id;
            $expense->payment_date = $request->payment_date;
        } else {
            $expense->payroll_month = $request->payroll_month;
            $expense->payroll_year = $request->payroll_year;
        }

        $expense->save();

        if ($expense->payment_status == 1) {
            $this->updatingStatusAccountEntries($expense);
        }

        // Notifying to User
        if ($expense->status == 'approved') {
            Notify::send('employee_expense_approve', $expense);
        } else if ($expense->status == 'rejected') {
            Notify::send('employee_expense_reject', $expense);
        }

        return ApiResponse::make("Status updated successfully", ['status' => 'success']);
    }

    public function updatingStatusAccountEntries(Expense $expense)
    {
        CommonHrm::insertAccountEntries($expense->account_id, null, "expense", $expense->payment_date, $expense->id, $expense->amount);
        CommonHrm::updateAccountAmount($expense->account_id);
    }
}

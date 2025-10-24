<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Expense\IndexRequest;
use App\Http\Requests\Api\Self\Expense\StoreRequest;
use App\Http\Requests\Api\Self\Expense\UpdateRequest;
use App\Http\Requests\Api\Self\Expense\DeleteRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class ExpenseController extends ApiBaseController
{
    protected $model = Expense::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // status Filters
        if ($request->has('status') && $request->status != "") {
            $expenseStatus = $request->status;
            $query = $query->where('expenses.status', $expenseStatus);
        }

        $query = $query->where('expenses.user_id', $loggedUser->id);

        if ($request->has('year')) {
            $query = $query->whereYear('date_time', $request->year);
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('date_time', $request->month);
        }

        return  $query;
    }
    public function storing(Expense $expense)
    {
        $loggedUser = user();
        $expense->user_id = $loggedUser->id;
        $expense->expense_type = 'employee_claims';
        $expense->payment_status = 0;
        $expense->payee_id = null;
        $expense->account_id = null;
        $expense->payroll_month = null;
        $expense->payroll_year = null;
        $expense->payee_id = null;
        $expense->payment_date = null;

        return $expense;
    }

    public function stored($expense)
    {
        // Notifying to company
        Notify::send('company_employee_expense_apply', $expense);

        return $expense;
    }

    public function updating(Expense $expense)
    {
        $loggedUser = user();

        if ($expense->getOriginal('user_id') != $loggedUser->id || $expense->getOriginal('status') != 'pending') {
            throw new ApiException('You are not valid user to change this.');
        }

        $expense->expense_type = 'employee_claims';
        $expense->payment_status = 0;
        $expense->payee_id = null;
        $expense->account_id = null;
        $expense->payroll_month = null;
        $expense->payroll_year = null;
        $expense->payment_date = null;

        return $expense;
    }

    public function destroying(Expense $expense)
    {
        $loggedUser = user();

        if ($expense->user_id != $loggedUser->id || $expense->status != 'pending') {
            throw new ApiException('You are not valid user to change this.');
        }

        return $expense;
    }

    public function getExpenseCategories()
    {
        $allExpenseCategory = ExpenseCategory::select('id', 'name', 'expense_for', 'description')
            ->whereIn('expense_for', ['employee_specific', 'all'])
            ->get()
            ->toArray();

        return ApiResponse::make('Success', $allExpenseCategory);
    }
}

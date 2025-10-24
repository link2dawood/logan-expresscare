<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\ExpenseCategory\IndexRequest;
use App\Http\Requests\Api\ExpenseCategory\StoreRequest;
use App\Http\Requests\Api\ExpenseCategory\UpdateRequest;
use App\Http\Requests\Api\ExpenseCategory\DeleteRequest;
use App\Models\ExpenseCategory;

class ExpenseCategoryController extends ApiBaseController
{
	protected $model = ExpenseCategory::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	protected function modifyIndex($query)
	{
		$request = request();

		// First apply filter from "expense_type"
		if ($request->has('expense_type') && $request->get('expense_type') !== null) {
			$expenseType = $request->get('expense_type');

			if ($expenseType === 'employee_claims') {
				$query->whereIn('expense_for', ['employee_specific', 'all']);
			} elseif ($expenseType === 'company_claims') {
				$query->whereIn('expense_for', ['company_specific', 'all']);
			}
		}

		// Then apply filter from "expense_for"
		if ($request->has('expense_for') && $request->get('expense_for') !== null) {
			$expenseFor = $request->get('expense_for');

			if ($expenseFor === 'employee_specific') {
				$query->where('expense_for', 'employee_specific');
			} elseif ($expenseFor === 'company_specific') {
				$query->where('expense_for', 'company_specific');
			} elseif ($expenseFor === 'all') {
				$query->whereIn('expense_for', ['employee_specific', 'company_specific', 'all']);
			}
		}

		return $query;
	}
}

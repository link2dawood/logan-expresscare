<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Payroll\IndexRequest;
use App\Models\Payroll;

class PayrollController extends ApiBaseController
{
    protected $model = Payroll::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        $query = $query->where('payrolls.user_id', $loggedUser->id);
        // Year Filters
        if ($request->has('year') && $request->year != "") {
            $payrollYear = $request->year;
            $query = $query->where('payrolls.year', $payrollYear);
        }

        // Month Filters
        if ($request->has('month') && $request->month != "") {
            $payrollMonth = $request->month;
            $query = $query->where('payrolls.month', $payrollMonth);
        }

        return  $query;
    }
}

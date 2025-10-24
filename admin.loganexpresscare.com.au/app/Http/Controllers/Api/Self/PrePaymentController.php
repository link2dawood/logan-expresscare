<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\PrePayment\IndexRequest;
use App\Models\PrePayment;

class PrePaymentController extends ApiBaseController
{
    protected $model = PrePayment::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // Dates Filters
        $query = $query->where('pre_payments.user_id', $loggedUser->id);

        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('DATE(pre_payments.date_time) >= ?', [$startDate])
                ->whereRaw('DATE(pre_payments.date_time) <= ?', [$endDate]);
        }

        if($request->has('year')){
            $query = $query->whereYear('date_time', $request->year);
        }

        if($request->has('month')){
            $query = $query->whereMonth('date_time', $request->month);
        }

        return  $query;
    }
}

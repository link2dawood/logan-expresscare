<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Warning\IndexRequest;
use App\Models\Warning;

class WarningController extends ApiBaseController
{
    protected $model = Warning::class;

    protected $indexRequest = IndexRequest::class;

    public function modifyIndex($query)
    {
        $request = request();
        $loggedUser = user();

        if ($request->has('warning_date') && $request->warning_date != '') {
            $warningDate = explode(',', $request->warning_date);
            $startDate = $warningDate[0];
            $endDate = $warningDate[1];

            $query = $query->whereRaw('warnings.warning_date >= ?', [$startDate])
                ->whereRaw('warnings.warning_date <= ?', [$endDate]);
        }

        $query = $query->where('warnings.user_id', $loggedUser->id);

        if($request->has('year')){
            $query = $query->whereYear('warning_date', $request->year);
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('warning_date', $request->month);
        }

        return $query;
    }
}
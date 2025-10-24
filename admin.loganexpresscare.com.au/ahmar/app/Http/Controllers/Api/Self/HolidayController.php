<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Holiday\IndexRequest;
use App\Models\Holiday;

class HolidayController extends ApiBaseController
{
    protected $model = Holiday::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $request = request();

        // isWeekend Filters
        if ($request->has('holiday_type') && $request->holiday_type != "all") {
            $holidayType = $request->holiday_type == 'holiday' ? 0 : 1;
            $query = $query->where('holidays.is_weekend', $holidayType);
        };

        if ($request->has('month')) {
            $query = $query->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $query = $query->whereYear('date', $request->year);
        }

        return  $query;
    }
}

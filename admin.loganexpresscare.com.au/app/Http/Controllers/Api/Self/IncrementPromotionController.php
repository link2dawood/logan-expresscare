<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\IncrementPromotion\IndexRequest;
use App\Models\IncrementPromotion;

class IncrementPromotionController extends ApiBaseController
{
    protected $model = IncrementPromotion::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // Dates Filters
        $query = $query->where('increments_promotions.user_id', $loggedUser->id);

        if ($request->has('dates') && $request->dates != "") {
            $dates = explode(',', $request->dates);
            $startDate = $dates[0];
            $endDate = $dates[1];

            $query = $query->whereRaw('increments_promotions.date >= ?', [$startDate])
                ->whereRaw('increments_promotions.date <= ?', [$endDate]);
        }

        // status Filters
        if ($request->has('type') && $request->type != "all") {
            $incrementPromotionType = $request->type;
            $query = $query->where('increments_promotions.type', $incrementPromotionType);
        }

        if($request->has('year')){
            $query = $query->whereYear('date', $request->year);
        }

        if($request->has('month')){
            $query = $query->whereMonth('date', $request->month);
        }

        return  $query;
    }
}

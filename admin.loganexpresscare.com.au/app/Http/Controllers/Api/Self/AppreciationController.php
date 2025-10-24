<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Appreciation\IndexRequest;
use App\Models\Appreciation;
use App\Models\Award;
use Examyou\RestAPI\ApiResponse;

class AppreciationController extends ApiBaseController
{
    protected $model = Appreciation::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();

        $query = $query->where('user_id', $user->id);

        if ($request->has('month')) {
            $query = $query->whereMonth('date', $request->month);
        }

        if ($request->has('year')) {
            $query = $query->whereYear('date', $request->year);
        }

        return  $query;
    }

    public function getAwards()
    {
        $allAwards = Award::select('id', 'name')->get()->toArray();

        return ApiResponse::make('Success', $allAwards);
    }
}

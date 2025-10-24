<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Opening\IndexRequest;
use App\Models\JobCategory;
use App\Models\Location;
use App\Models\Opening;
use Examyou\RestAPI\ApiResponse;

class OpeningsController extends ApiBaseController
{
    protected $model = Opening::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $query = $query->where('openings.active', '=', 1)
            ->where(function ($query) {
                $query->where('openings.visible_to', "both")
                    ->orWhere('openings.visible_to', "internal_employee");
            });

        return $query;
    }

    public function getJobCategories()
    {
        $allExpenseCategory = JobCategory::select('id', 'name')->get()->toArray();

        return ApiResponse::make('Success', $allExpenseCategory);
    }

    public function getLocations()
    {
        $allLocations = Location::select('id', 'name')->get()->toArray();

        return ApiResponse::make('Success', $allLocations);
    }
}

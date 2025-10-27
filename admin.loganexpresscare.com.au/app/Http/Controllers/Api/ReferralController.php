<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Referral\IndexRequest;
use App\Http\Requests\Api\Referral\StoreRequest;
use App\Http\Requests\Api\Referral\UpdateRequest;
use App\Http\Requests\Api\Referral\DeleteRequest;
use App\Models\Referral;
use Examyou\RestAPI\ApiResponse;

class ReferralController extends ApiBaseController
{
    protected $model = Referral::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // Apply visibility filters
        if ($loggedUser->ability('admin', 'referrals_view')) {
            $query = $this->applyVisibility($query, 'referrals');
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status != "all" && $request->status != "") {
            $query = $query->where('referrals.status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('date_range') && $request->date_range != '') {
            $dateRange = explode(',', $request->date_range);
            if (count($dateRange) == 2) {
                $startDate = $dateRange[0];
                $endDate = $dateRange[1];
                $query = $query->whereRaw('referrals.submission_date >= ?', [$startDate])
                    ->whereRaw('referrals.submission_date <= ?', [$endDate]);
            }
        }

        return $query;
    }
}

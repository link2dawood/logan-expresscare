<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\Notify;
use Carbon\Carbon;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Resignation\IndexRequest;
use App\Http\Requests\Api\Self\Resignation\StoreRequest;
use App\Http\Requests\Api\Self\Resignation\UpdateRequest;
use App\Http\Requests\Api\Self\Resignation\DeleteRequest;
use App\Models\Offboarding;

class ResignationController extends ApiBaseController
{
    protected $model = Offboarding::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();

        $request = request();
        $query = $query->where('offboardings.type', 'resignation');

        if ($request->has('status') && $request->status != "") {
            $query = $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $resignationDate = explode(',', $request->start_date);
            $startDate = $resignationDate[0];
            $endDate = $resignationDate[1];

            $query = $query->whereRaw('offboardings.start_date >= ?', [$startDate])
                ->whereRaw('offboardings.start_date <= ?', [$endDate]);
        }

        $query = $query->where('user_id', $loggedUser->id);

        if ($request->has('year')) {
            $query = $query->whereYear('start_date', $request->year);
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('start_date', $request->month);
        }

        return  $query;
    }

    public function storing($offboarding)
    {
        $loggedUser = user();

        $offboarding->manager_id = null;
        $offboarding->type = 'resignation';
        $offboarding->submit_date = Carbon::now()->format('Y-m-d H:i:s');
        $offboarding->status = 'pending';
        $offboarding->user_id = $loggedUser->id;

        return $offboarding;
    }

    public function stored($offboarding)
    {
        // Notifying to company
        Notify::send('company_employee_resignation_apply', $offboarding);

        return $offboarding;
    }
}

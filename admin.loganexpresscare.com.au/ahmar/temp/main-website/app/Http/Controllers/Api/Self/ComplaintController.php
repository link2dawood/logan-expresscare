<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Complaint\IndexRequest;
use App\Http\Requests\Api\Self\Complaint\StoreRequest;
use App\Http\Requests\Api\Self\Complaint\UpdateRequest;
use App\Http\Requests\Api\Self\Complaint\DeleteRequest;
use App\Models\Self\SelfComplaint;
use Examyou\RestAPI\Exceptions\ApiException;

class ComplaintController extends ApiBaseController
{
    protected $model = SelfComplaint::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();

        if ($request->has('reports') && $request->reports == "by_you") {
            $query = $query->where('from_user_id', $user->id);
        } else {
            $query = $query->where('to_user_id', $user->id)
                ->where('status', 'approved');
        }

        if ($request->has('year')) {
            $query = $query->whereYear('date_time', $request->year);
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('date_time', $request->month);
        }

        return  $query;
    }

    public function storing($complaint)
    {
        $user = user();

        $complaint->from_user_id = $user->id;
        $complaint->status = 'pending';
        $complaint->manager_id = null;

        return $complaint;
    }

    public function stored($complaint)
    {
        // Notifying to company
        Notify::send('company_employee_complaint_apply', $complaint);

        return $complaint;
    }

    public function updating(SelfComplaint $complaint)
    {
        $loggedUser = user();

        if ($complaint->getOriginal('from_user_id') != $loggedUser->id || $complaint->getOriginal('status') != 'pending') {
            throw new ApiException('You are not valid user to change this.');
        }

        return $complaint;
    }

    public function destroying(SelfComplaint $complaint)
    {
        $loggedUser = user();

        if ($complaint->from_user_id != $loggedUser->id || $complaint->status != 'pending') {
            throw new ApiException('You are not valid user to change this.');
        }

        return $complaint;
    }
}

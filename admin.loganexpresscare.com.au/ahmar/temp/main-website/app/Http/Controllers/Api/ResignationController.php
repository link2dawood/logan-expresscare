<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use Carbon\Carbon;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Resignation\IndexRequest;
use App\Http\Requests\Api\Resignation\StoreRequest;
use App\Http\Requests\Api\Resignation\UpdateRequest;
use App\Http\Requests\Api\Resignation\ResignationChangeStatusRequest;
use App\Http\Requests\Api\Resignation\DeleteRequest;
use App\Models\Generate;
use App\Models\Offboarding;
use Examyou\RestAPI\ApiResponse;

class ResignationController extends ApiBaseController
{
    protected $model = Offboarding::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $resignationStoreRequest = ResignationChangeStatusRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();
        $query = $query->where('offboardings.type', 'resignation');

        if ($request->has('status') && $request->status != "") {
            $query = $query->where('offboardings.status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $resignationDate = explode(',', $request->start_date);
            $startDate = $resignationDate[0];
            $endDate = $resignationDate[1];

            $query = $query->whereRaw('offboardings.start_date >= ?', [$startDate])
                ->whereRaw('offboardings.start_date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'resignations_view')) {
            $query = $this->applyVisibility($query, 'offboardings');

            if ($request->has('user_id')) {
                $query = $query->where('offboardings.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('offboardings.user_id', $loggedUser->id);
        }

        return  $query;
    }

    public function storing($offboarding)
    {
        $request = request();
        $loggedUser = user();

        $offboarding->type = 'resignation';
        $offboarding->manager_id = $loggedUser->id;
        $offboarding->status = $request->status;
        $offboarding->submit_date = Carbon::now()->format('Y-m-d H:i:s');


        if ($offboarding->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->user_id = $offboarding->user_id;
            $generate->letterhead_template_id = $offboarding->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $offboarding->generates_id = $generate->id;
        }

        return $offboarding;
    }

    public function stored($offboarding)
    {
        // Notifying to User
        if ($offboarding->status == 'approved') {
            Notify::send('employee_resignation_approve', $offboarding);
        } else if ($offboarding->status == 'rejected') {
            Notify::send('employee_resignation_reject', $offboarding);
        }

        return $offboarding;
    }

    public function updating($offboarding)
    {
        $request = request();
        $loggedUser = user();
        $offboarding->type = 'resignation';
        $offboarding->manager_id = $loggedUser->id;
        $offboarding->status = $request->status;

        // Create and update letter head generate
        CommonHrm::createUpdateGenerate($offboarding);

        return $offboarding;
    }

    public function updateResignationStatus(ResignationChangeStatusRequest $request)
    {
        $loggedUser = user();
        $request = request();

        $id = $this->getIdFromHash($request->xid);
        $resignation = Offboarding::find($id);
        $resignation->reply_notes = $request->reply_notes;
        $resignation->end_date = $request->end_date;
        $resignation->manager_id = $loggedUser->id;

        if ($request->status == 'approved') {
            $resignation->status = 'approved';
        } else if ($request->status == 'rejected') {
            $resignation->status = 'rejected';
        }

        $resignation->save();

        // Notifying to User
        if ($resignation->status == 'approved') {
            Notify::send('employee_resignation_approve', $resignation);
        } else if ($request->status == 'rejected') {
            Notify::send('employee_resignation_reject', $resignation);
        }

        return ApiResponse::make('Resignation Status Update successfully', [
            'status' => 'success'
        ]);
    }
}

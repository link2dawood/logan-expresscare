<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Complaint\IndexRequest;
use App\Http\Requests\Api\Complaint\StoreRequest;
use App\Http\Requests\Api\Complaint\UpdateRequest;
use App\Http\Requests\Api\Complaint\DeleteRequest;
use App\Http\Requests\Api\Complaint\UpdateStatusRequest;
use App\Models\Complaint;
use App\Models\Generate;
use Examyou\RestAPI\ApiResponse;

class ComplaintController extends ApiBaseController
{
    protected $model = Complaint::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {

        $loggedUser = user();

        $request = request();

        if ($request->has('complaintStatus') && $request->complaintStatus != '') {
            $query = $query->where('complaints.status', $request->complaintStatus);
        }

        if ($request->has('date_time') && $request->date_time != '') {
            $resignationDate = explode(',', $request->date_time);
            $startDate = $resignationDate[0];
            $endDate = $resignationDate[1];

            $query = $query->whereRaw('complaints.date_time >= ?', [$startDate])
                ->whereRaw('complaints.date_time <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'complaints_view')) {
            $query = $this->applyVisibility($query, 'complaints', 'to_user_id');

            if ($request->has('from_user_id')) {
                $query = $query->where('complaints.from_user_id', $this->getIdFromHash($request->from_user_id));
            }

            if ($request->has('to_user_id')) {
                $query = $query->where('complaints.to_user_id', $this->getIdFromHash($request->to_user_id));
            }
        } else {
            $query = $query->where('complaints.to_user_id', $loggedUser->id)
                ->where('complaints.status', 'approved');
        }

        return  $query;
    }

    public function storing(Complaint $complaint)
    {
        $loggedUser = user();
        $request = request();

        $complaint->manager_id = $loggedUser->id;

        if ($complaint->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->user_id = $complaint->to_user_id;
            $generate->letterhead_template_id = $complaint->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $complaint->generates_id = $generate->id;
        }

        return $complaint;
    }

    public function stored($complaint)
    {
        // Notifying to User
        if ($complaint->status == 'approved') {
            Notify::send('employee_complaint_approve', $complaint);
        } else if ($complaint->status == 'rejected') {
            Notify::send('employee_complaint_reject', $complaint);
        }

        return $complaint;
    }

    public function updating(Complaint $complaint)
    {
        $loggedUser = user();
        $request = request();

        $complaint->manager_id = $loggedUser->id;

        // Previous no letter head template selected but now selected
        if ($complaint->getOriginal('letterhead_template_id') == '' && $complaint->letterhead_template_id != '' && $request->letterhead_description != '') {
            $generate = new Generate();
            $generate->user_id = $complaint->to_user_id;
            $generate->letterhead_template_id = $complaint->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $complaint->generates_id = $generate->id;
        } else if ($complaint->letterhead_template_id && $request->letterhead_description != '' && $complaint->generates_id) {
            // Previous letter head template selected and now new one selected
            $generate = Generate::find($complaint->generates_id);
            $generate->user_id = $complaint->to_user_id;
            $generate->letterhead_template_id = $complaint->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->save();
        } else if ($complaint->getOriginal('letterhead_template_id') != '' && $complaint->letterhead_template_id == '' && $complaint->generates_id) {
            // Previous letter head template selected and generate exists but now letterhead templated not selected
            Generate::destroy($complaint->generates_id);
        }

        return $complaint;
    }

    public function changeStatus(UpdateStatusRequest $request)
    {
        $loggedUser = user();
        $request = request();

        $id = $this->getIdFromHash($request->xid);
        $complaint = Complaint::find($id);
        $complaint->reply_notes = $request->reply_notes;

        if ($request->status == 'approved' || $request->status == 'rejected') {
            $complaint->status = $request->status;
        }

        $complaint->manager_id = $loggedUser->id;
        $complaint->save();

        // Notifying to User
        if ($complaint->status == 'approved') {
            Notify::send('employee_complaint_approve', $complaint);
        } else if ($complaint->status == 'rejected') {
            Notify::send('employee_complaint_reject', $complaint);
        }

        return ApiResponse::make("Status updated successfully", ['status' => 'success']);
    }
}

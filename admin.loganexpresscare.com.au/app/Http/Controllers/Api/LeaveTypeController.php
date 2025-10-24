<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LeaveType\IndexRequest;
use App\Http\Requests\Api\LeaveType\StoreRequest;
use App\Http\Requests\Api\LeaveType\UpdateRequest;
use App\Http\Requests\Api\LeaveType\DeleteRequest;
use App\Models\EmployeeSpecificLeaveCount;
use App\Models\LeaveType;
use Examyou\RestAPI\ApiResponse;

class LeaveTypeController extends ApiBaseController
{
    protected $model = LeaveType::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;


    public function storing(LeaveType $leaveType)
    {
        $request = request();
        $loggedUser = user();

        $leaveType->created_by = $loggedUser->id;
        $leaveType->max_leaves_per_month = $leaveType->is_paid ? $request->max_leaves_per_month : null;

        return $leaveType;
    }

    public function updating(LeaveType $leaveType)
    {
        $request = request();
        $leaveType->max_leaves_per_month = $leaveType->is_paid ? $request->max_leaves_per_month : null;

        return $leaveType;
    }

    public function storeUpdateEmployeeSpecificLeave()
    {
        $request = request();

        $allFormFields = $request['all_form_fields'] ?? [];
        $removedFields = $request['removed_fields'] ?? [];

        // Step 1: Remove entries by xid
        if (!empty($removedFields)) {
            $idsToRemove = array_map(function ($xid) {
                return $this->getIdFromHash($xid);
            }, $removedFields);

            EmployeeSpecificLeaveCount::whereIn('id', $idsToRemove)->delete();
        }

        // Step 2: Update or Create entries
        $leaveTypeId = null;

        foreach ($allFormFields as $formField) {
            $leaveTypeId = $this->getIdFromHash($formField['leave_type_id']);
            $userId = $this->getIdFromHash($formField['user_id']);

            // Decode xid if exists, used for update
            $recordId = null;
            if (!empty($formField['xid'])) {
                $recordId = $this->getIdFromHash($formField['xid']);
            }

            // If xid is given, try to find and update, otherwise create
            $record = $recordId ? EmployeeSpecificLeaveCount::find($recordId) : null;

            if ($record) {
                $record->update([
                    'user_id' => $userId,
                    'leave_type_id' => $leaveTypeId,
                    'total_leaves' => $formField['total_leaves'],
                ]);
            } else {
                EmployeeSpecificLeaveCount::create([
                    'user_id' => $userId,
                    'leave_type_id' => $leaveTypeId,
                    'total_leaves' => $formField['total_leaves'],
                ]);
            }
        }

        // Step 3: Return updated list
        $data = [];
        if ($leaveTypeId) {
            $data = EmployeeSpecificLeaveCount::where('leave_type_id', $leaveTypeId)->get();
        }

        return ApiResponse::make('Success', ["data" => $data]);
    }
}

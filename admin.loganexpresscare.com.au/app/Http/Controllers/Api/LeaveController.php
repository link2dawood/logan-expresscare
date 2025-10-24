<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Carbon\Carbon;
use App\Models\Company;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Classes\Common;
use App\Http\Requests\Api\Leave\IndexRequest;
use App\Http\Requests\Api\Leave\StoreRequest;
use App\Http\Requests\Api\Leave\UpdateRequest;
use App\Http\Requests\Api\Leave\DeleteRequest;
use App\Http\Requests\Api\Leave\RemainingLeavesRequest;
use App\Http\Requests\Api\Leave\UnpaidLeavesRequest;
use App\Http\Requests\Api\Leave\PaidLeavesRequest;
use App\Http\Requests\Api\Leave\UpdateStatusRequest;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\EmployeeSpecificLeaveCount;
use Illuminate\Support\Facades\DB;

class LeaveController extends ApiBaseController
{
    protected $model = Leave::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        // status Filters
        if ($request->has('status') && $request->status != "all") {
            $leaveStatus = $request->status;
            $query = $query->where('leaves.status', $leaveStatus);
        }

        if ($loggedUser->ability('admin', 'leaves_view')) {
            $query = $this->applyVisibility($query, 'leaves');

            if ($request->has('user_id')) {
                $query = $query->where('leaves.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('leaves.user_id', $loggedUser->id);
        }

        // attendance_month Filters
        // This is used to filter leaves by month, e.g., "2023-10
        if ($request->has('attendance_month')) {
            $attendanceMonth = $request->attendance_month; // "YYYY-MM"

            $query->whereRaw("DATE_FORMAT(leaves.start_date, '%Y-%m') = ?", [$attendanceMonth]);
        }

        // leaveType Filters
        if ($request->has('leave_type_id') && $request->leave_type_id != "") {
            $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
            $query = $query->where('leaves.leave_type_id', $leaveTypeId);
        }

        return  $query;
    }

    public function storing(Leave $leave)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('status') && $loggedUser->ability('admin', 'leaves_edit')) {
            $leave->status = $request->status;
        }

        if ($request->has('user_id') && $loggedUser->ability('admin', 'leaves_edit')) {
            $leave->user_id = $this->getIdFromHash($request->user_id);
        } else {
            $leave->user_id = $loggedUser->id;
        }

        $leave->total_days = Common::calculateLeaveDays($leave, $leave->start_date, $leave->end_date);

        // Throw exception if attendance already exists
        CommonHrm::checkIfAttendanceAlreadyExists($leave->user_id, $leave->start_date, $leave->end_date);

        return $leave;
    }

    public function stored(Leave $leave)
    {
        $loggedUser = user();

        if ($leave->status == 'approved' && $loggedUser->ability('admin', 'leaves_approve_reject')) {
            CommonHrm::updateLeaveStatus($leave->id);
        }

        return $leave;
    }

    public function updating(Leave $leave)
    {
        $loggedUser = user();
        $request = request();

        // Can not update the leave if it is already approved or rejcted
        if ($leave->getOriginal('status') == 'approved' || $leave->getOriginal('status') == 'rejected') {
            throw new ApiException("Leave already apporved or rejected");
        }

        // If not admin or not having persmission of leaves_approve_reject
        // Then cannot edit other user
        if (!$loggedUser->ability('admin', 'leaves_edit_all') && $leave->user_id != $loggedUser->id) {
            throw new ApiException("Not have valid permission");
        }

        if (($request->status == 'approved' || $request->status == 'rejected')  && $loggedUser->ability('admin', 'leaves_approve_reject')) {
            if ($request->status == 'approved') {
                CommonHrm::updateLeaveStatus($leave->id);
            }

            $leave->status = $request->status;
        }

        return $leave;
    }

    public function destroying(Leave $leave)
    {
        $loggedUser = user();

        // If user don't have permssion to leaves_delete_all permission
        if (!$loggedUser->ability('admin', 'leaves_delete_all') && $leave->user_id == $loggedUser->id && ($leave->status == 'approved' || $leave->status == 'rejected')) {
            throw new ApiException("Not have valid permission");
        }

        // Cannot delete other user leave if not have permission
        if (!$loggedUser->ability('admin', 'leaves_delete_all') && $leave->user_id != $loggedUser->id && $leave->status == 'pending') {
            throw new ApiException("Not have valid permission");
        }

        return $leave;
    }

    public function statusUpdate(UpdateStatusRequest $request, $id)
    {
        $loggedUser = user();
        $id = $this->getIdFromHash($id);
        $leave = Leave::find($id);

        if ($leave->status == 'approved' || $leave->status == 'rejected') {
            throw new ApiException("Leave already approved or rejected");
        }

        if (!$loggedUser->ability('admin', 'leaves_approve_reject')) {
            throw new ApiException("Not have valid permission");
        }

        // Initialize $data to avoid undefined variable error
        $data = [];

        if ($request->status == 'approved') {
            $data = CommonHrm::updateLeaveStatus($id);
        }

        // Updating Status
        $leave->status = $request->status;
        $leave->save();

        // Notifying to User
        if ($request->status == 'approved' || $request->status == 'rejected') {
            $emailTemplate = $request->status == 'approved' ? 'employee_leave_approve' : 'employee_leave_reject';
            Notify::send($emailTemplate, $leave);
        }

        return ApiResponse::make('Success', $data);
    }

    public function leaveStartMonthUpdate(UpdateLeaveSettingsRequest $request)
    {

        $user = user();
        $company = Company::find($user->company_id);
        $company->leave_start_month = $request->leave_start_month;
        $company->save();

        return ApiResponse::make('Success', []);
    }


    public function remainingLeaves(RemainingLeavesRequest $request)
    {
        $loggedUser = user();
        $userId = $loggedUser->ability('admin', 'leaves_edit') && $request->has('user_id') ? $this->getIdFromHash($request->user_id) : $loggedUser->id;

        if (!$loggedUser->ability('admin', 'leaves_view')) {
            throw new ApiException("Not have valid permission");
        }

        $allLeaveTypes = LeaveType::select('id', 'name', 'total_leaves', 'is_paid', 'count_type')
            ->with(['employeeSpecificLeaveCount'])
            ->get();
        $year = $request->year;

        $fincialDates = CommonHrm::getFincialYearStartEndDate($year);
        $startDate = $fincialDates['startDate'];
        $endDate = $fincialDates['endDate'];

        // Filter leave types according to user assignment logic
        $hasEmployeeSpecific = $allLeaveTypes->filter(function ($leaveType) use ($userId) {
            return $leaveType->count_type === 'employee_specific' && $leaveType->employeeSpecificLeaveCount->contains(function ($entry) use ($userId) {
                return $entry->user_id === $userId;
            });
        })->count() > 0;

        $filteredLeaveTypes = $allLeaveTypes->filter(function ($leaveType) use ($userId, $hasEmployeeSpecific) {
            if ($leaveType->count_type === 'same_for_all') {
                return true;
            }
            if ($leaveType->count_type === 'employee_specific') {
                return $hasEmployeeSpecific && $leaveType->employeeSpecificLeaveCount->contains(function ($entry) use ($userId) {
                    return $entry->user_id === $userId;
                });
            }
            return false;
        })->values();

        foreach ($filteredLeaveTypes as $leaveType) {
            if ($leaveType->count_type === 'employee_specific') {
                $userSpecific = $leaveType->employeeSpecificLeaveCount->first(function ($entry) use ($userId) {
                    return $entry->user_id === $userId;
                });
                if ($userSpecific) {
                    $leaveType->total_leaves = $userSpecific->total_leaves;
                }
            }
            $totalFullDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->where('is_holiday', 0)
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.leave_type_id', $leaveType->id)
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 0);

            if ($leaveType->is_paid == 1) {
                $totalFullDayLeavesCount = $totalFullDayLeavesCount->where('attendances.is_paid', 1);
            }
            $totalFullDayLeavesCount = $totalFullDayLeavesCount->count();

            $totalHalfDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->where('is_holiday', 0)
                ->whereBetween('attendances.date', [$startDate, $endDate])
                ->where('attendances.leave_type_id', $leaveType->id)
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 1);

            if ($leaveType->is_paid == 1) {
                $totalHalfDayLeavesCount = $totalHalfDayLeavesCount->where('attendances.is_paid', 1);
            }
            $totalHalfDayLeavesCount = $totalHalfDayLeavesCount->count();

            $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;
            $leaveType->remaining_leaves = $leaveType->total_leaves - $totalLeaves;
        }

        return ApiResponse::make('Data fetched', [
            'data' => $filteredLeaveTypes
        ]);
    }

    public function unpaidLeaves(UnpaidLeavesRequest $request)
    {
        $company = company();
        $startMonth = (int)$company->leave_start_month;

        // Check if user have permssion to view all leaves
        $loggedUser = user();
        $userId = $loggedUser->ability('admin', 'leaves_edit') && $request->has('user_id') ? $this->getIdFromHash($request->user_id) : $loggedUser->id;

        if (!$loggedUser->ability('admin', 'leaves_view')) {
            throw new ApiException("Not have valid permission");
        }

        $year = (int)$request->year;
        $yearMonths = [];

        $monthCounter = 0;
        for ($i = $startMonth; $i <= 12; $i++) {
            $yearMonths[] = [
                'month' => $i,
                'year'  => $year
            ];

            $monthCounter += 1;
        }

        for ($i = 1; $i <= 12 - $monthCounter; $i++) {
            $yearMonths[] = [
                'month' => $i,
                'year'  => $year + 1
            ];
        }

        $unpaidLeaveData = [];
        foreach ($yearMonths as $yearMonth) {
            $totalFullDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->whereNotNull('attendances.leave_type_id')
                ->whereMonth('attendances.date', $yearMonth['month'])
                ->whereYear('attendances.date', $yearMonth['year'])
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 0)
                ->where('attendances.is_paid', 0)
                ->count();

            $totalHalfDayLeavesCount = Attendance::where('attendances.is_leave', 1)
                ->whereNotNull('attendances.leave_type_id')
                ->whereMonth('attendances.date', $yearMonth['month'])
                ->whereYear('attendances.date', $yearMonth['year'])
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_half_day', 1)
                ->where('attendances.is_paid', 0)
                ->count();

            $totalLeaves = ($totalHalfDayLeavesCount / 2) + $totalFullDayLeavesCount;

            $unpaidLeaveData[] = [
                'month' => $yearMonth['month'] < 10 ? '0' . $yearMonth['month'] : '' . $yearMonth['month'],
                'year'  => $yearMonth['year'],
                'unpaid_leaves' => $totalLeaves
            ];
        }

        return ApiResponse::make('Data fetched', [
            'data' => $unpaidLeaveData
        ]);
    }

    public function paidLeaves(PaidLeavesRequest $request)
    {
        $company = company();
        $startMonth = (int)$company->leave_start_month;

        $loggedUser = user();
        $userId = $loggedUser->ability('admin', 'leaves_edit') && $request->has('user_id')
            ? $this->getIdFromHash($request->user_id)
            : $loggedUser->id;

        if (!$loggedUser->ability('admin', 'leaves_view')) {
            throw new ApiException("Not have valid permission");
        }

        $year = (int)$request->year;
        $yearMonths = [];

        // Build company leave cycle months
        $monthCounter = 0;
        for ($i = $startMonth; $i <= 12; $i++) {
            $yearMonths[] = ['month' => $i, 'year' => $year];
            $monthCounter++;
        }
        for ($i = 1; $i <= 12 - $monthCounter; $i++) {
            $yearMonths[] = ['month' => $i, 'year' => $year + 1];
        }

        // Only get PAID leave types for the company
        $allLeaveTypes = LeaveType::where('company_id', $company->id)
            ->where('is_paid', 1)
            ->get();

        $paidLeaveData = [];

        foreach ($yearMonths as $yearMonth) {
            // Get actual taken leaves for this month
            $takenLeaves = Attendance::query()
                ->join('leave_types', 'leave_types.id', '=', 'attendances.leave_type_id')
                ->select(
                    'leave_types.id as leave_type_id',
                    DB::raw("SUM(CASE WHEN attendances.is_half_day = 1 THEN 0.5 ELSE 1 END) as total_days")
                )
                ->where('attendances.is_leave', 1)
                ->whereNotNull('attendances.leave_type_id')
                ->where('attendances.user_id', $userId)
                ->where('attendances.is_paid', 1)
                ->whereMonth('attendances.date', $yearMonth['month'])
                ->whereYear('attendances.date', $yearMonth['year'])
                ->groupBy('leave_types.id')
                ->pluck('total_days', 'leave_type_id')
                ->toArray();

            $leaveCounts = [];

            foreach ($allLeaveTypes as $leaveType) {
                // Check employee_specific restriction
                if ($leaveType->count_type === 'employee_specific') {
                    $hasAccess = EmployeeSpecificLeaveCount::where('user_id', $userId)
                        ->where('leave_type_id', $leaveType->id)
                        ->exists();

                    if (!$hasAccess) {
                        continue;
                    }
                }

                $leaveCounts[$leaveType->name] = isset($takenLeaves[$leaveType->id])
                    ? (float)$takenLeaves[$leaveType->id]
                    : 0.0;
            }

            $paidLeaveData[] = [
                'month' => str_pad($yearMonth['month'], 2, '0', STR_PAD_LEFT),
                'year' => $yearMonth['year'],
                'leave_counts' => $leaveCounts
            ];
        }

        return ApiResponse::make('Data fetched', [
            'data' => $paidLeaveData
        ]);
    }
}

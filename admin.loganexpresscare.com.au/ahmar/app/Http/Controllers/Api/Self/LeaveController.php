<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Classes\Common;
use App\Http\Requests\Api\Self\Leave\IndexRequest;
use App\Http\Requests\Api\Self\Leave\StoreRequest;
use App\Http\Requests\Api\Self\Leave\UpdateRequest;
use App\Http\Requests\Api\Self\Leave\DeleteRequest;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\EmployeeSpecificLeaveCount;
use Carbon\Carbon;
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

        $query = $query->where('leaves.user_id', $loggedUser->id);

        // leaveType Filters
        if ($request->has('leave_type_id') && $request->leave_type_id != "") {
            $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
            $query = $query->where('leaves.leave_type_id', $leaveTypeId);
        }

        if ($request->has('year')) {
            $query->where(function ($query) use ($request) {
                $query->whereYear('start_date', $request->year)
                    ->orWhereYear('end_date', $request->year);
            });
        }

        if ($request->has('month')) {
            $query->where(function ($query) use ($request) {
                $query->whereMonth('start_date', $request->month)
                    ->orWhereMonth('end_date', $request->month);
            });
        }

        return  $query;
    }

    public function storing(Leave $leave)
    {
        $loggedUser = user();
        $request = request();
        $leave->status = 'pending';

        $leave->user_id = $loggedUser->id;
        $leave->total_days = Common::calculateLeaveDays($leave, $leave->start_date, $leave->end_date);

        // Throw exception if attendance already exists
        CommonHrm::checkIfAttendanceAlreadyExists($leave->user_id, $leave->start_date, $leave->end_date);

        return $leave;
    }

    public function stored(Leave $leave)
    {
        // Notifying to company
        Notify::send('company_employee_leave_apply', $leave);

        return $leave;
    }

    public function updating(Leave $leave)
    {
        $loggedUser = user();
        $request = request();

        $leave->total_days = Common::calculateLeaveDays($leave, $leave->start_date, $leave->end_date);

        // Can not update the leave if it is already approved or rejcted
        if ($request->has('status') && $leave->getOriginal('status') == 'approved' || $leave->getOriginal('status') == 'rejected') {
            throw new ApiException("Leave already apporved or rejected");
        }

        // If not admin or not having persmission of leaves_approve_reject
        // Then cannot edit other user
        if ($leave->user_id != $loggedUser->id) {
            throw new ApiException("Not have valid permission");
        }

        return $leave;
    }

    public function destroying(Leave $leave)
    {
        $loggedUser = user();

        // Cannot delete approved or rejected leaves
        if ($leave->status == 'approved' || $leave->status == 'rejected') {
            throw new ApiException("Not have valid permission");
        }

        // Cannot delete other user leave if not have permission
        if ($leave->user_id != $loggedUser->id) {
            throw new ApiException("Not have valid permission");
        }

        return $leave;
    }

    public function getLeavesType()
    {
        $allLeaveTypes = LeaveType::select('id', 'name')->get();

        return ApiResponse::make('Success', [
            'data' => $allLeaveTypes
        ]);
    }

    public function remainingLeaves()
    {
        $request = request();
        $loggedUser = user();
        $userId = $loggedUser->id;
        $year = $request->year;

        // Eager load employeeSpecificLeaveCount for filtering
        $allLeaveTypes = LeaveType::with('employeeSpecificLeaveCount')->select('id', 'name', 'total_leaves', 'is_paid', 'count_type')->get();

        $fincialDates = CommonHrm::getFincialYearStartEndDate($year);
        $startDate = $fincialDates['startDate'];
        $endDate = $fincialDates['endDate'];

        // Check if user has any employee-specific leave type assigned
        $hasEmployeeSpecific = $allLeaveTypes->filter(function ($leaveType) use ($userId) {
            return $leaveType->count_type === 'employee_specific' &&
                $leaveType->employeeSpecificLeaveCount->contains(function ($entry) use ($userId) {
                    return $entry->user_id === $userId;
                });
        })->count() > 0;

        // Filter leave types for this user
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
            // Set total_leaves from employee-specific count if applicable
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

    public function unpaidLeaves()
    {
        $company = company();
        $startMonth = (int)$company->leave_start_month;
        $request = request();

        // Check if user have permssion to view all leaves
        $loggedUser = user();
        $userId = $loggedUser->id;
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
    public function filterLeaves()
    {
        $loggedUser = user();
        $leaveTypes = LeaveType::with(['employeeSpecificLeaveCount' => function ($q) use ($loggedUser) {
            $q->where('user_id', $loggedUser->id);
        }])
            ->where(function ($q) use ($loggedUser) {
                $q->where('count_type', 'same_for_all')
                    ->orWhere(function ($q2) use ($loggedUser) {
                        $q2->where('count_type', 'employee_specific')
                            ->whereHas('employeeSpecificLeaveCount', function ($q3) use ($loggedUser) {
                                $q3->where('user_id', $loggedUser->id);
                            });
                    });
            })
            ->get();

        return ApiResponse::make('Success', ['data' => $leaveTypes]);
    }

    public function paidLeaves()
    {
        $company = company();
        $request = request();
        $startMonth = (int) $company->leave_start_month;

        // Always use logged-in user
        $loggedUser = user();
        $userId = $loggedUser->id;

        $year = (int) $request->year;
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
                    ? (float) $takenLeaves[$leaveType->id]
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

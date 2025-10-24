<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use App\Classes\CommonHrm;
use App\Exports\LangExport;
use App\Http\Requests\Api\Attendance\IndexRequest;
use App\Http\Requests\Api\Attendance\StoreRequest;
use App\Http\Requests\Api\Attendance\UpdateRequest;
use App\Http\Requests\Api\Attendance\DeleteRequest;
use App\Http\Requests\Api\Attendance\ImportRequest;
use App\Http\Requests\Api\Attendance\UserAttendaceRequest;
use App\Imports\AttendanceImport;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Models\Attendance;
use App\Models\Lang;
use App\Models\WorkDuration;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends ApiBaseController
{
    protected $model = Attendance::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $importRequest = ImportRequest::class;
    protected $userAttendanceRequest = UserAttendaceRequest::class;


    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();
        if ($request->has('date') && $request->date != '') {
            $date = explode(',', $request->date);
            $startDate = $date[0];
            $endDate = $date[1];

            $query = $query->whereRaw('attendances.date >= ?', [$startDate])
                ->whereRaw('attendances.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'attendances_view')) {
            $query = $this->applyVisibility($query, 'attendances');

            if ($request->has('user_id')) {
                $userId = $this->getIdFromHash($request->user_id);
                $query = $query->where('attendances.user_id', $userId);
            }
        } else {
            $query = $query->where('attendances.user_id', $loggedUser->id);
        }

        if ($request->has('status') && $request->status != "all") {
            $attendance = $request->status;
            $query = $query->where('attendances.status', $attendance);
        };

        return  $query;
    }

    public function storing($attendance)
    {
        return $this->updateAddEditing($attendance);
    }

    public function stored($attendance)
    {
        return $this->createAndUpdateDurations($attendance, 'add');
    }

    public function updating($attendance)
    {
        return $this->updateAddEditing($attendance, 'edit');
    }


    public function updated($attendance)
    {
        return $this->createAndUpdateDurations($attendance, 'edit');
    }

    public function createAndUpdateDurations($attendance, $type)
    {
        $lastWork = WorkDuration::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($lastWork) {

            $clockOutTime = $attendance->clock_out_time;
            $start = Carbon::createFromFormat('H:i:s', $lastWork->start_time);
            $end = Carbon::createFromFormat('H:i:s', $clockOutTime);

            if ($end->lt($start)) {
                // Handles overnight shift crossing midnight
                $end->addDay();
            }

            $duration = $start->diffInMinutes($end);
            $lastWork->end_time = $clockOutTime;
            $lastWork->duration = $duration;
            $lastWork->save();
        } else {
            if ($type == 'add') {
                $workDuration = new WorkDuration();
                $workDuration->attendance_id = $attendance->id;
                $workDuration->start_time = $attendance->clock_in_time;
                $workDuration->end_time = $attendance->clock_out_time;
                $workDuration->duration = $attendance->total_duration;
                $workDuration->status = 'started';
                $workDuration->save();
            }
        }
    }

    // public function updateAddEditing($attendance, $addEditType = 'add')
    // {
    //     $company = company();
    //     $request = request();

    //     $userId = $this->getIdFromHash($request->user_id);

    //     $officeShiftTiming = CommonHrm::getUserClockingTime($userId);

    //     $clockIn = $request->clock_in_time;
    //     $clockOut = $request->clock_out_time;


    //     $date = $request->date;

    //     // Throw exception if attendance already exists
    //     if ($addEditType == 'add' || ($attendance->isDirty('date') && $addEditType == 'edit')) {
    //         CommonHrm::checkIfAttendanceAlreadyExists($userId, $date);
    //     }



    //     $attendance->office_clock_in_time = $officeShiftTiming['clock_in_time'];
    //     $attendance->office_clock_out_time = $officeShiftTiming['clock_out_time'];

    //     $attendance->actual_clock_in_date = $date;


    //     // For inserting the clocking date time
    //     $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockIn, $company->timezone)
    //         ->setTimezone('UTC');

    //     $totalMinutes = CommonHrm::getMinutesFromTimes($clockIn, $clockOut);
    //     $clockoutDateTime = $clockInDateTime->copy()->addMinutes($totalMinutes);
    //     $attendance->clock_in_date_time = $clockInDateTime->copy()->format('Y-m-d H:i:s');
    //     $attendance->clock_out_date_time = $clockoutDateTime->format('Y-m-d H:i:s');
    //     $attendance->total_duration = $totalMinutes;

    //     // No need of timezone because we need the date object

    //     if ($request->has('is_half_day') && $request->is_half_day == 1 && $request->has('leave_type_id') && $request->leave_type_id != '') {
    //         $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
    //         $attendance->is_leave = 1;
    //         $attendance->is_half_day = 1;
    //         $attendance->leave_type_id = $leaveTypeId;

    //         // Check and update leave type is paid or unpaid
    //         $isPaidOrNot = CommonHrm::isPaidLeaveOrNot($date, $userId, $leaveTypeId);
    //         $attendance->is_paid = $isPaidOrNot['isPaid'];
    //     } else {

    //         $attendance->is_leave = 0;
    //         $attendance->is_half_day = 0;
    //         $attendance->leave_type_id = null;
    //         $attendance->is_paid = 1;
    //     }

    //     // For changing the status
    //     if ($attendance->is_half_day) {
    //         $attendance->status = "half_day";
    //     } else if ($attendance->leave_type_id) {
    //         $attendance->status = "on_leave";
    //     } else {
    //         $attendance->status = "present";
    //     }

    //     return $attendance;
    // }

    public function updateAddEditing($attendance, $addEditType = 'add')
    {
        $company = company();
        $request = request();
        $userId = $this->getIdFromHash($request->user_id);
        $officeShiftTiming = CommonHrm::getUserClockingTime($userId);

        $clockIn = $request->clock_in_time;   // e.g., "23:00:00"
        $clockOut = $request->clock_out_time; // e.g., "07:00:00"
        $date = $request->date;               // e.g., "2025-05-28"

        $isOvernightShift = strtotime($officeShiftTiming['clock_in_time']) >= strtotime($officeShiftTiming['clock_out_time']);
        $clockInSeconds = strtotime($clockIn);
        $clockOutSeconds = strtotime($clockOut);

        $totalMinutesOfShift = commonHrm::getMinutesFromTimes(
            $officeShiftTiming['clock_in_time'],
            $officeShiftTiming['clock_out_time']
        );

        $actualShiftClockInTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $officeShiftTiming['clock_in_time'], $company->timezone);


        if ($isOvernightShift && (1440 > $totalMinutesOfShift) && ($clockInSeconds >= strtotime('00:00:00')) && ($clockInSeconds <= strtotime($officeShiftTiming['actual_allowed_clock_out_time']))) {
            $actualDateIn = Carbon::parse($date)->addDay()->format('Y-m-d');
        } else if ($isOvernightShift && (1440 <= $totalMinutesOfShift) && ($clockInSeconds >= strtotime('00:00:00')) && ($clockInSeconds < strtotime($officeShiftTiming['actual_allowed_clock_in_time']))) {
            $actualDateIn = Carbon::parse($date)->addDay()->format('Y-m-d');
        } else {
            $actualDateIn = Carbon::parse($date)->format('Y-m-d');
        }

        if ($isOvernightShift && ($clockOutSeconds > strtotime('00:00:00')) && ($clockOutSeconds <= strtotime($officeShiftTiming['actual_allowed_clock_out_time']))) {
            $actualDateOut = Carbon::parse($date)->addDay()->format('Y-m-d');
        } else {
            $actualDateOut = Carbon::parse($date)->format('Y-m-d');
        }

        try {
            $userClockIn = Carbon::createFromFormat('Y-m-d H:i:s', $actualDateIn . ' ' . $clockIn, $company->timezone);
            $userClockOut = Carbon::createFromFormat('Y-m-d H:i:s', $actualDateOut . ' ' . $clockOut, $company->timezone);
        } catch (\Exception $e) {
            throw new ApiException("Invalid clock-in or clock-out time format.");
        }


        // Check for duplicate attendance
        if ($addEditType === 'add' || ($attendance->isDirty('date') && $addEditType === 'edit')) {
            CommonHrm::checkIfAttendanceAlreadyExists($userId, $date);
        }


        if ($userClockOut->lt($userClockIn)) {
            throw new ApiException("Clock-out time " . $userClockOut->format('d M Y h:i A') . " cannot be earlier than clock-in time: " . $userClockIn->format('d M Y h:i A'));
        }

        if ($userClockIn->eq($userClockOut)) {
            throw new ApiException("Clock-in and clock-out time cannot be the same.");
        }


        if ($request->is_half_day == 0 || $request->is_half_day == false) {
            // 1. Build allowed clock-in datetime in company's timezone
            $allowedClockIn = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $officeShiftTiming['clock_in_time'], $company->timezone);

            // 2. Add grace period (late mark after X minutes)
            $allowedClockIn->addMinutes($officeShiftTiming['late_mark_after']);

            // 3. Compare
            if ($userClockIn->greaterThan($allowedClockIn)) {
                $attendance->is_late = true;
            } else {
                $attendance->is_late = false;
            }
        }

        // Assign values
        $attendance->office_clock_in_time = $officeShiftTiming['clock_in_time'];
        $attendance->office_clock_out_time = $officeShiftTiming['clock_out_time'];
        $attendance->early_clock_in_time = $officeShiftTiming['early_clock_in_time'];
        $attendance->allow_clock_out_till = $officeShiftTiming['allow_clock_out_till'];
        $attendance->actual_clock_in_date = $date;
        $attendance->clock_in_date_time = $userClockIn->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $attendance->clock_out_date_time = $userClockOut->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $attendance->total_duration = $userClockIn->diffInMinutes($userClockOut);

        // $attendance->late_time_duration = ($isLate == 1)? CommonHrm::getMinutesFromTimes($details['office_clock_in_time'], $details['time']):0;
        // Leave handling
        if ($request->has('is_half_day') && $request->is_half_day == 1 && $request->has('leave_type_id') && $request->leave_type_id !== '') {
            $leaveTypeId = $this->getIdFromHash($request->leave_type_id);
            $attendance->is_leave = 1;
            $attendance->is_half_day = 1;
            $attendance->leave_type_id = $leaveTypeId;

            $isPaidOrNot = CommonHrm::isPaidLeaveOrNot($date, $userId, $leaveTypeId);
            $attendance->is_paid = $isPaidOrNot['isPaid'];
        } else {
            $attendance->is_leave = 0;
            $attendance->is_half_day = 0;
            $attendance->leave_type_id = null;
            $attendance->is_paid = 1;
        }

        // Status
        if ($attendance->is_half_day) {
            $attendance->status = "half_day";
        } elseif ($attendance->leave_type_id) {
            $attendance->status = "on_leave";
        } else {
            $attendance->status = "present";
        }

        return $attendance;
    }



    public function attendanceSummary()
    {
        $detail = CommonHrm::attendanceDetail();

        return ApiResponse::make('Data fetched', [
            'columns' => $detail['columns'],
            'dateRange' => $detail['dateRange'],
            'data' => $detail['data'],
            'meta'  => [
                'paging' => [
                    'total' => $detail['totalRecords']
                ]
            ]
        ]);
    }

    public function attendanceSummaryByMonth()
    {
        $result = CommonHrm::attendanceDetailByMonth();

        return ApiResponse::make('Data fetched', $result);
    }

    public function import(ImportRequest $request)
    {
        $company = company();
        if ($request->hasFile('file')) {
            $override = filter_var($request->input('override', false), FILTER_VALIDATE_BOOLEAN);
            Excel::import(new AttendanceImport($override, $company), $request->file('file'));
        }

        return ApiResponse::make('Imported Successfully', []);
    }

    public function addUserAttendance(UserAttendaceRequest $request)
    {
        $company = company();
        $userIds = $request->user_ids ?? [];
        $isHalfDay = $request->is_half_day ?? 0;
        $leaveTypeId = $isHalfDay ? $this->getIdFromHash($request->leave_type_id) : null;
        $overwrite = $request->attendanceOverwrite;
        $markBy = $request->mark_attendance_by;

        $dates = [];

        if ($markBy === 'month' && $request->attendance_month) {
            $month = (int)substr($request->attendance_month, 5, 2);
            $year = (int)substr($request->attendance_month, 0, 4);

            $startDate = Carbon::createFromDate($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            while ($startDate->lte($endDate)) {
                $dates[] = $startDate->format('Y-m-d');
                $startDate->addDay();
            }
        } elseif ($markBy === 'date' && is_array($request->date) && count($request->date) === 2) {
            $startDate = Carbon::parse($request->date[0])->startOfDay();
            $endDate = Carbon::parse($request->date[1])->startOfDay();

            while ($startDate->lte($endDate)) {
                $dates[] = $startDate->format('Y-m-d');
                $startDate->addDay();
            }
        }

        $allCreatedAttendances = [];

        foreach ($userIds as $encodedUserId) {
            $userId = $this->getIdFromHash($encodedUserId);

            foreach ($dates as $date) {
                $existing = Attendance::where('user_id', $userId)
                    ->whereDate('date', $date)
                    ->first();

                if ($existing && !$overwrite) {
                    continue; // Skip if attendance already exists and not overwriting
                }

                if (!$existing) {
                    $existing = new Attendance();
                }

                $userClockIn = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $request->clock_in_time, $company->timezone);
                $userClockOut = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $request->clock_out_time, $company->timezone);

                if ($userClockOut->lt($userClockIn)) {
                    $userClockOut->addDay();
                }

                $existing->company_id = $company->id;
                $existing->user_id = $userId;
                $existing->date = $date;
                $existing->clock_in_time = $request->clock_in_time;
                $existing->clock_out_time = $request->clock_out_time;
                $existing->clock_in_date_time = $userClockIn->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $existing->clock_out_date_time = $userClockOut->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $existing->total_duration = $userClockIn->diffInMinutes($userClockOut);
                $existing->is_late = $request->is_late ?? 0;
                $existing->is_half_day = $isHalfDay;
                $existing->is_leave = $isHalfDay;
                $existing->leave_type_id = $leaveTypeId;
                $existing->reason = $request->reason;
                $existing->save();

                // After saving attendance, insert into work_durations
                if ($existing->clock_in_time !== null) {
                    $totalDuration = $existing->clock_out_time
                        ? CommonHrm::getMinutesFromTimes($existing->clock_in_time, $existing->clock_out_time)
                        : CommonHrm::getMinutesFromTimes($existing->clock_in_time, $existing->office_clock_out_time);

                    $workDuration = DB::table('work_durations')
                        ->where('attendance_id', $existing->id)
                        ->first();
                    // dd($workDuration, $overwrite);
                    if ($workDuration && $overwrite) {
                        // Update existing work_duration record
                        DB::table('work_durations')
                            ->where('attendance_id', $existing->id)
                            ->update([
                                'start_time' => $existing->clock_in_time,
                                'end_time'   => $existing->clock_out_time ?? $existing->office_clock_out_time,
                                'duration'   => $totalDuration,
                                'updated_at' => now(),
                            ]);
                    } elseif (!$workDuration) {
                        // Insert new work_duration record
                        DB::table('work_durations')->insert([
                            'company_id'    => $existing->company_id,
                            'attendance_id' => $existing->id,
                            'start_time'    => $existing->clock_in_time,
                            'end_time'      => $existing->clock_out_time ?? $existing->office_clock_out_time,
                            'duration'      => $totalDuration,
                            'status'        => 'started',
                            'notes'         => null,
                            'created_at'    => $existing->created_at,
                            'updated_at'    => $existing->updated_at,
                        ]);
                    }
                }


                $allCreatedAttendances[] = $existing;
            }
        }

        return ApiResponse::make('Attendance records added successfully.', ['data' => $allCreatedAttendances]);
    }
}

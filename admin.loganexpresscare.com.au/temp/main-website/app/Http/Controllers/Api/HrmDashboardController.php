<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Models\Company;
use App\Models\StaffMember;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\WorkDuration;
use App\Http\Requests\Api\HrmSettings\UpdateHrmSettingsRequest;
use App\Models\Expense;

class HrmDashboardController extends ApiBaseController
{

    public function getTodayAttendanceDetails()
    {
        $ipAddress = CommonHrm::getIpAddress();
        $details = CommonHrm::getTodayAttendanceDetails();

        $details['ip_address'] = $ipAddress;
        // Get work duration status if exists
        $attendance = Attendance::where('user_id', user()->id)
            ->whereDate('date',  $details['date'])
            ->first();

        if ($attendance) {
            $workDuration = WorkDuration::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->latest()
                ->first();

            if ($workDuration) {
                $details['attendance_status'] = $workDuration->status;
            }
        }

        return ApiResponse::make('Details Fetched Successfully', $details);
    }

    public function markTodayAttendance()
    {
        $request = request();
        $ipAddress = CommonHrm::getIpAddress();
        $details = CommonHrm::getTodayAttendanceDetails();
        $attendanceType = $request->type;
        $user = user();
        $company = company();


        $shiftClockInTime = CommonHrm::getUserClockingTime($user->id);

        if ($shiftClockInTime['allowed_ip_address'] && count($shiftClockInTime['allowed_ip_address']) > 0) {
            if (in_array($ipAddress, $shiftClockInTime['allowed_ip_address']) == false) {
                throw new ApiException("You can not mark attendance from this IP address");
            }
        }

        if ($attendanceType == 'clock-in') {
            if ($details['is_on_full_day_leave']) {
                throw new ApiException("You are on leave.");
            } else if ($details['is_clocked_in']) {
                throw new ApiException("Already Clocked In");
            } else if ($details['office_hours_expired']) {
                throw new ApiException("Office hours passed");
            } else {
                $isLate = 0;
                $shiftClockInTime = CommonHrm::getUserClockingTime($user->id);
                $currentDateTimeObject = Carbon::now($company->timezone);
                // Check if is late or not
                if ($shiftClockInTime && $shiftClockInTime['late_mark_after']) {
                    $clockInDateTimeFormat = Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTimeObject->copy()->format('Y-m-d') . ' ' . $shiftClockInTime['clock_in_time'], $company->timezone)
                        ->addMinutes($shiftClockInTime['late_mark_after']);

                    $isLate = CommonHrm::isLateClockedIn($clockInDateTimeFormat->format('H:i:s'), $currentDateTimeObject->copy()->format('H:i:s'), $currentDateTimeObject->copy()->format('Y-m-d'), $details['date']);
                }
                if ($details && $details['id']) {

                    $attendance = Attendance::find($details['id']);
                } else {

                    $attendance = new Attendance();
                    $attendance->date = $details['date'];
                    $attendance->user_id = $user->id;
                    $attendance->late_time_duration = ($isLate == 1) ? CommonHrm::getMinutesFromTimes($details['office_clock_in_time'], $details['time']) : 0;
                    $attendance->is_late = $isLate;
                }

                // Mark the clockIn

                $attendance->actual_clock_in_date = $currentDateTimeObject->copy()->format('Y-m-d');
                $attendance->clock_in_date_time = $currentDateTimeObject->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $attendance->clock_in_ip_address = $ipAddress;
                $attendance->clock_in_time = $details['time'];
                $attendance->office_clock_in_time = $details['office_clock_in_time'];
                $attendance->office_clock_out_time = $details['office_clock_out_time'];
                $attendance->early_clock_in_time = $shiftClockInTime['early_clock_in_time'];
                $attendance->allow_clock_out_till = $shiftClockInTime['allow_clock_out_till'];
                $attendance->is_paid = 1;

                $attendance->clock_in_latitude = $request->has('latitude') ? $request->latitude : null;
                $attendance->clock_in_longitude = $request->has('longitude') ? $request->longitude : null;
                $attendance->clock_in_location_name = CommonHrm::getLocationNameFromGooleGeocoding($attendance->clock_in_latitude, $attendance->clock_in_longitude);
                $attendance->clock_in_location_accuracy = $request->has('location_accuracy') ? $request->location_accuracy : null;
                $attendance->clock_in_browser = $request->has('browser') ? $request->browser : null;
                $attendance->clock_in_platform = $request->has('platform') ? $request->platform : null;
                $attendance->clock_in_device_type = $request->has('device_type') ? $request->device_type : null;
                $attendance->clock_in_user_agent = $request->has('user_agent') ? $request->user_agent : null;

                $attendance->save();


                // Create initial work duration
                $workDuration = new WorkDuration();
                $workDuration->attendance_id = $attendance->id;
                $workDuration->start_time = Carbon::now($company->timezone)->format('H:i:s');


                $workDuration->status = 'started';
                $workDuration->save();

                $resultData = Attendance::with(['workDuration'])->find($attendance->id);

                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;
                $newDetails['attendance_status'] = 'started';
                $newDetails['latest_attendance'] = $resultData;

                // Notifying to company
                Notify::send('company_employee_clock_in', $attendance);

                return ApiResponse::make('Clocked In Successfully', $newDetails);
            }
        } else if ($attendanceType == 'pause') {
            if ($details['is_clocked_in'] == false) {
                throw new ApiException("You have not clocked in");
            }
            $attendance = Attendance::where('date', $details['date'])
                ->where('user_id', $user->id)
                ->first();

            if ($attendance) {
                // Get the latest work duration
                $lastStarted = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNull('end_time')
                    ->latest()
                    ->first();


                if ($lastStarted) {
                    $now = Carbon::now($company->timezone)->format('H:i:s');
                    $start = Carbon::createFromFormat('H:i:s', $lastStarted->start_time);
                    $duration = $this->getMinutesFromTimes($lastStarted->start_time, $now);
                    // $duration = $start->diffInMinutes($now);

                    $lastStarted->end_time = Carbon::now($company->timezone)->format('H:i:s');
                    $lastStarted->duration = $duration;
                    $lastStarted->save();
                }

                $pausedDuration = new WorkDuration();
                $pausedDuration->attendance_id = $attendance->id;
                $pausedDuration->start_time = Carbon::now($company->timezone)->format('H:i:s');
                $pausedDuration->status = 'paused';
                $pausedDuration->notes = $request->reason;
                $pausedDuration->save();

                // After saving pausedDuration

                if ($attendance) {
                    if ($attendance) {
                        $this->updateTotalDuration($attendance);
                    }
                }

                $resultData = Attendance::with(['workDuration'])->find($attendance->id);
                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;
                $newDetails['attendance_status'] = 'paused';
                $newDetails['latest_attendance'] = $resultData;

                return ApiResponse::make('Work paused successfully', $newDetails);
            }
        } else if ($attendanceType == 'start') {
            if ($details['is_clocked_in'] == false) {
                throw new ApiException("You have not clocked in");
            }

            $attendance = Attendance::where('date', $details['date'])
                ->where('user_id', $user->id)
                ->first();

            if ($attendance) {
                // Create new work duration
                $lastPaused = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'paused')
                    ->whereNull('end_time')
                    ->latest()
                    ->first();

                if ($lastPaused) {
                    $now = Carbon::now($company->timezone)->format('H:i:s');
                    $start = Carbon::createFromFormat('H:i:s', $lastPaused->start_time);
                    // dd($lastPaused->start_time);
                    $duration = $this->getMinutesFromTimes($lastPaused->start_time, $now);
                    // $duration = $start->diffInMinutes($now);

                    $lastPaused->end_time = $now;
                    $lastPaused->duration = $duration;
                    $lastPaused->save();
                }

                // Create new 'started' entry
                $newStart = new WorkDuration();
                $newStart->attendance_id = $attendance->id;
                $newStart->start_time = Carbon::now($company->timezone)->format('H:i:s');
                $newStart->status = 'started';
                $newStart->save();

                // After saving newStart

                if ($attendance) {
                    if ($attendance) {
                        $this->updateTotalDuration($attendance);
                    }
                }

                $resultData = Attendance::with(['workDuration'])->find($attendance->id);
                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;
                $newDetails['attendance_status'] = 'started';
                $newDetails['latest_attendance'] = $resultData;

                return ApiResponse::make('Work started successfully', $newDetails);
            }
        } else if ($attendanceType == 'clock-out') {
            if ($details['is_clocked_in'] == false) {
                throw new ApiException("You have not clocked in");
            } else if ($details['is_clocked_out']) {
                throw new ApiException("Already Clocked Out");
            } else if ($details['office_hours_expired']) {
                throw new ApiException("Office Hours Expired");
            } else {
                $currentDateTimeObject = Carbon::now($company->timezone);

                $attendance = Attendance::where('date', $details['date'])
                    ->where('user_id', $user->id)
                    ->first();

                if ($attendance) {
                    $attendance->clock_out_date_time = $currentDateTimeObject->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                    $attendance->clock_out_ip_address = $ipAddress;
                    $attendance->clock_out_time = $details['time'];

                    $attendance->clock_out_latitude = $request->has('latitude') ? $request->latitude : null;
                    $attendance->clock_out_longitude = $request->has('longitude') ? $request->longitude : null;
                    $attendance->clock_out_location_name = CommonHrm::getLocationNameFromGooleGeocoding($attendance->clock_out_latitude, $attendance->clock_out_longitude);
                    $attendance->clock_out_location_accuracy = $request->has('location_accuracy') ? $request->location_accuracy : null;
                    $attendance->clock_out_browser = $request->has('browser') ? $request->browser : null;
                    $attendance->clock_out_platform = $request->has('platform') ? $request->platform : null;
                    $attendance->clock_out_device_type = $request->has('device_type') ? $request->device_type : null;
                    $attendance->clock_out_user_agent = $request->has('user_agent') ? $request->user_agent : null;

                    // End any active work duration
                    $lastWork = WorkDuration::where('attendance_id', $attendance->id)
                        ->whereNull('end_time')
                        ->latest()
                        ->first();

                    if ($lastWork) {
                        $now = Carbon::now($company->timezone)->format('H:i:s');
                        $start = Carbon::createFromFormat('H:i:s', $lastWork->start_time);
                        $duration = $this->getMinutesFromTimes($lastWork->start_time, $now);

                        $lastWork->end_time = $now;
                        $lastWork->duration = $duration;
                        $lastWork->save();
                    }

                    if ($attendance) {
                        $this->updateTotalDuration($attendance);
                    }
                }
                $resultData = Attendance::with(['workDuration'])->find($attendance->id);
                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;
                $newDetails['latest_attendance'] = $resultData;

                // Notifying to company
                Notify::send('company_employee_clock_out', $attendance);

                return ApiResponse::make('Clocked Out Successfully', $newDetails);
            }
        }
    }

    public function updateTotalDuration($attendance)
    {
        $attendance->total_duration = WorkDuration::where('attendance_id', $attendance->id)->sum('duration');
        $attendance->save();
    }

    public function pendingLeaves()
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'leaves_approve_reject')) {
            return ApiResponse::make('Details Fetched Successfully', [
                'pending_leaves' => []
            ]);
        }

        $allPendingLeaves = Leave::select('id', 'user_id', 'status', 'reason', 'start_date', 'end_date', 'is_half_day')
            ->with(
                ['user:id,name,profile_image', 'leaveType:id,name']
            )->where('status', 'pending')->orderBy('start_date', 'asc')
            ->get();
        return ApiResponse::make('Details Fetched Successfully', [
            'pending_leaves' => $allPendingLeaves
        ]);
    }

    public function pendingExpense()
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'expenses_approve_reject')) {
            return ApiResponse::make('Details Fetched Successfully', [
                'pending_expense' => []
            ]);
        }

        $allPendingExpense = Expense::select('id', 'user_id', 'status', 'notes', 'date_time', 'amount', 'reference_number', 'bill')
            ->with([
                'user:id,name,profile_image',
                'expenseCategory:id,name',
                'payee:id,name',
                'account:id,name'
            ])
            ->where('status', 'pending')
            ->orderBy('date_time', 'asc')
            ->get();

        return ApiResponse::make('Details Fetched Successfully', [
            'pending_expense' => $allPendingExpense
        ]);
    }

    public function todayAttendanceUsers()
    {
        $company = company();
        $allUsersLists = [];
        $allUsers = StaffMember::select('id', 'name')->get();

        $currentDateTimeObject = Carbon::now($company->timezone);
        $currentTime = $currentDateTimeObject->copy()->format('H:i:s');
        $currentDate = $currentDateTimeObject->copy()->format('Y-m-d');

        $todayHoliday = Holiday::whereDate('date', $currentDate)->first();
        $isHoliday = $todayHoliday ? true : false;

        $allAttendances = Attendance::whereDate('attendances.date', $currentDate)
            ->get();

        foreach ($allUsers as $allUser) {
            $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($currentDate, $allUser) {
                return $allAttendance->date->format('Y-m-d') == $currentDate && $allAttendance->user_id == $allUser->id;
            })->first();

            if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->is_leave) {
                    $status = 'absent';
                } else {
                    $status = 'present';
                }
            } else {
                $status = 'not_marked';
            }

            $allUsersLists[] = [
                'name' => $allUser->name,
                'status' => $status,
                'profile_image_url' => $allUser->profile_image_url,
            ];
        }

        return ApiResponse::make('Users Fetched Successfully', [
            'users' => $allUsersLists,
            'is_holiday' => $isHoliday,
            'holiday' => $todayHoliday,
        ]);
    }


    public function updateHrmSettings(UpdateHrmSettingsRequest $request)
    {
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'hrm_settings')) {
            throw new ApiException("Not have valid permission");
        }

        $company = Company::find($loggedUser->company_id);
        $company->leave_start_month = $request->leave_start_month;
        $company->clock_in_time = $request->clock_in_time;
        $company->clock_out_time = $request->clock_out_time;
        $company->late_mark_after = $request->late_mark_after;
        $company->self_clocking = $request->self_clocking;
        $company->early_clock_in_time = $request->early_clock_in_time;
        $company->allow_clock_out_till = $request->allow_clock_out_till;
        $company->allowed_ip_address = $request->allowed_ip_address;
        $company->profile_image = $request->profile_image;
        $company->employee_id_prefix = $request->employee_id_prefix;
        $company->employee_id_start = $request->employee_id_start;
        $company->employee_id_digits = $request->employee_id_digits;
        $company->capture_location = $request->capture_location;
        $company->save();

        // Reseting the company settings
        company(true);

        return ApiResponse::make('Success', []);
    }

    public function getMinutesFromTimes($startTime, $endTime)
    {

        $timeArray = $this->getDetailsArrayFromTimes($startTime, $endTime);
        $isNextDayForTime = $this->isTimeForNextDate($startTime, $endTime);

        if ($isNextDayForTime) {
            $totalMinutes = ((24 - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  ($timeArray['end_hours'] * 60) +  $timeArray['end_minutes'];
        } else {
            $totalMinutes =  (($timeArray['end_hours'] - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  $timeArray['end_minutes'];
        }

        return $totalMinutes;
    }

    public function getDetailsArrayFromTimes($startTime, $endTime)
    {
        $startTimeArray = explode(':', $startTime);
        $endTimeArray = explode(':', $endTime);

        $startTimeHour = $startTimeArray[0];
        $startTimeMinute = $startTimeArray[1];

        $endTimeHour = $endTimeArray[0];
        $endTimeMinute = $endTimeArray[1];

        return [
            'start_hours' => (int) $startTimeHour,
            'start_minutes' => (int) $startTimeMinute,
            'end_hours' => (int) $endTimeHour,
            'end_minutes' => (int) $endTimeMinute,
        ];
    }

    public function isTimeForNextDate($startTime, $endTime)
    {

        $timeArray = $this->getDetailsArrayFromTimes($startTime, $endTime);

        return $timeArray['start_hours'] > $timeArray['end_hours'] ? true : false;
    }
}

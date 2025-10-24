<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\WorkDuration;
use App\Classes\CommonHrm;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Examyou\RestAPI\Exceptions\ApiException;

class AttendanceService
{
    public function getTodayAttendanceDetails($userId)
    {
        try {
            $ipAddress = CommonHrm::getIpAddress();
            $details = CommonHrm::getTodayAttendanceDetails();
            $details['ip_address'] = $ipAddress;

            Log::info('Fetching today attendance details', [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'details' => $details
            ]);

            $attendance = Attendance::where('user_id', $userId)
                ->whereDate('date', Carbon::today())
                ->first();

            if ($attendance) {
                $workDuration = WorkDuration::where('attendance_id', $attendance->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($workDuration) {
                    $details['attendance_status'] = $workDuration->status;
                    Log::info('Found work duration status', [
                        'attendance_id' => $attendance->id,
                        'status' => $workDuration->status
                    ]);
                }
            }

            return $details;
        } catch (\Exception $e) {
            Log::error('Error in getTodayAttendanceDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function markAttendance($userId, $attendanceType, $ipAddress, $company)
    {
        try {
            $details = CommonHrm::getTodayAttendanceDetails();
            $shiftClockInTime = CommonHrm::getUserClockingTime($userId);

            Log::info('Marking attendance', [
                'user_id' => $userId,
                'type' => $attendanceType,
                'ip_address' => $ipAddress
            ]);

            if ($shiftClockInTime['allowed_ip_address'] && count($shiftClockInTime['allowed_ip_address']) > 0) {
                if (!in_array($ipAddress, $shiftClockInTime['allowed_ip_address'])) {
                    Log::warning('Invalid IP address for attendance', [
                        'ip_address' => $ipAddress,
                        'allowed_ips' => $shiftClockInTime['allowed_ip_address']
                    ]);
                    throw new ApiException("You can not mark attendance from this IP address");
                }
            }

            switch ($attendanceType) {
                case 'clock-in':
                    return $this->handleClockIn($userId, $details, $ipAddress, $company);
                case 'pause':
                    return $this->handlePause($userId, $details, $ipAddress);
                case 'start':
                    return $this->handleStart($userId, $details, $ipAddress);
                case 'clock-out':
                    return $this->handleClockOut($userId, $details, $ipAddress);
                default:
                    throw new ApiException("Invalid attendance type");
            }
        } catch (\Exception $e) {
            Log::error('Error in markAttendance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'attendance_type' => $attendanceType
            ]);
            throw $e;
        }
    }

    private function handleClockIn($userId, $details, $ipAddress, $company)
    {
        if ($details['is_on_full_day_leave']) {
            Log::warning('Attempted clock-in while on leave', ['user_id' => $userId]);
            throw new ApiException("You are on leave.");
        }
        if ($details['is_clocked_in']) {
            Log::warning('Attempted duplicate clock-in', ['user_id' => $userId]);
            throw new ApiException("Already Clocked In");
        }
        if ($details['office_hours_expired']) {
            Log::warning('Attempted clock-in after office hours', ['user_id' => $userId]);
            throw new ApiException("Office hours passed");
        }

        $isLate = 0;
        $shiftClockInTime = CommonHrm::getUserClockingTime($userId);
        $currentDateTimeObject = Carbon::now($company->timezone);

        if ($shiftClockInTime && $shiftClockInTime['late_mark_after']) {
            $clockInDateTimeFormat = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $currentDateTimeObject->copy()->format('Y-m-d') . ' ' . $shiftClockInTime['clock_in_time'],
                $company->timezone
            )->addMinutes($shiftClockInTime['late_mark_after']);

            $isLate = CommonHrm::isLateClockedIn(
                $clockInDateTimeFormat->format('H:i:s'),
                $currentDateTimeObject->copy()->format('H:i:s'),
                $currentDateTimeObject->copy()->format('Y-m-d'),
                $details['date']
            );

            Log::info('Late clock-in check', [
                'user_id' => $userId,
                'is_late' => $isLate,
                'clock_in_time' => $currentDateTimeObject->format('H:i:s'),
                'expected_time' => $clockInDateTimeFormat->format('H:i:s')
            ]);
        }

        $attendance = new Attendance();
        $attendance->date = $details['date'];
        $attendance->user_id = $userId;
        $attendance->actual_clock_in_date = $currentDateTimeObject->copy()->format('Y-m-d');
        $attendance->clock_in_date_time = $currentDateTimeObject->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $attendance->clock_in_ip_address = $ipAddress;
        $attendance->clock_in_time = $details['time'];
        $attendance->office_clock_in_time = $details['office_clock_in_time'];
        $attendance->office_clock_out_time = $details['office_clock_out_time'];
        $attendance->is_paid = 1;
        $attendance->is_late = $isLate;
        $attendance->save();

        Log::info('Successfully clocked in', [
            'attendance_id' => $attendance->id,
            'clock_in_time' => $attendance->clock_in_time,
            'is_late' => $isLate
        ]);

        $workDuration = new WorkDuration();
        $workDuration->attendance_id = $attendance->id;
        $workDuration->start_time = Carbon::now()->format('H:i:s');
        $workDuration->status = 'started';
        $workDuration->save();

        Log::info('Created initial work duration', [
            'work_duration_id' => $workDuration->id,
            'status' => 'started'
        ]);

        $newDetails = CommonHrm::getTodayAttendanceDetails();
        $newDetails['ip_address'] = $ipAddress;
        $newDetails['attendance_status'] = 'started';

        return [
            'message' => 'Clocked In Successfully',
            'details' => $newDetails
        ];
    }

    private function handlePause($userId, $details, $ipAddress)
    {
        if (!$details['is_clocked_in']) {
            Log::warning('Attempted pause without clocking in', ['user_id' => $userId]);
            throw new ApiException("You have not clocked in");
        }

        $attendance = Attendance::where('date', $details['date'])
            ->where('user_id', $userId)
            ->first();

        if ($attendance) {
            $workDuration = WorkDuration::where('attendance_id', $attendance->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($workDuration) {
                $workDuration->status = 'paused';
                $workDuration->end_time = Carbon::now()->format('H:i:s');
                $workDuration->save();

                Log::info('Successfully paused work', [
                    'work_duration_id' => $workDuration->id,
                    'end_time' => $workDuration->end_time
                ]);

                $newDetails = CommonHrm::getTodayAttendanceDetails();
                $newDetails['ip_address'] = $ipAddress;
                $newDetails['attendance_status'] = 'paused';

                return [
                    'message' => 'Work paused successfully',
                    'details' => $newDetails
                ];
            }
        }
    }

    private function handleStart($userId, $details, $ipAddress)
    {
        if (!$details['is_clocked_in']) {
            Log::warning('Attempted start without clocking in', ['user_id' => $userId]);
            throw new ApiException("You have not clocked in");
        }

        $attendance = Attendance::where('date', $details['date'])
            ->where('user_id', $userId)
            ->first();

        if ($attendance) {
            $workDuration = new WorkDuration();
            $workDuration->attendance_id = $attendance->id;
            $workDuration->start_time = Carbon::now()->format('H:i:s');
            $workDuration->status = 'started';
            $workDuration->save();

            Log::info('Successfully started work', [
                'work_duration_id' => $workDuration->id,
                'start_time' => $workDuration->start_time
            ]);

            $newDetails = CommonHrm::getTodayAttendanceDetails();
            $newDetails['ip_address'] = $ipAddress;
            $newDetails['attendance_status'] = 'started';

            return [
                'message' => 'Work started successfully',
                'details' => $newDetails
            ];
        }
    }

    private function handleClockOut($userId, $details, $ipAddress)
    {
        if (!$details['is_clocked_in']) {
            Log::warning('Attempted clock-out without clocking in', ['user_id' => $userId]);
            throw new ApiException("You have not clocked in");
        }
        if ($details['is_clocked_out']) {
            Log::warning('Attempted duplicate clock-out', ['user_id' => $userId]);
            throw new ApiException("Already Clocked Out");
        }
        if ($details['office_hours_expired']) {
            Log::warning('Attempted clock-out after office hours', ['user_id' => $userId]);
            throw new ApiException("Office Hours Expired");
        }

        $currentDateTimeObject = Carbon::now();
        $attendance = Attendance::where('date', $details['date'])
            ->where('user_id', $userId)
            ->first();

        if ($attendance) {
            $attendance->clock_out_date_time = $currentDateTimeObject->format('Y-m-d H:i:s');
            $attendance->clock_out_ip_address = $ipAddress;
            $attendance->clock_out_time = $details['time'];

            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $totalMinutes = CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->clock_out_time);
                $attendance->total_duration = $totalMinutes;
            }

            $attendance->save();

            Log::info('Successfully clocked out', [
                'attendance_id' => $attendance->id,
                'clock_out_time' => $attendance->clock_out_time,
                'total_duration' => $attendance->total_duration
            ]);

            $workDuration = WorkDuration::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->first();

            if ($workDuration) {
                $workDuration->end_time = Carbon::now()->format('H:i:s');
                $workDuration->save();

                Log::info('Ended active work duration', [
                    'work_duration_id' => $workDuration->id,
                    'end_time' => $workDuration->end_time
                ]);
            }
        }

        $newDetails = CommonHrm::getTodayAttendanceDetails();
        $newDetails['ip_address'] = $ipAddress;

        return [
            'message' => 'Clocked Out Successfully',
            'details' => $newDetails
        ];
    }
}

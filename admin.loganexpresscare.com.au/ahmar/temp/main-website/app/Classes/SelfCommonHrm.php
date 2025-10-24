<?php

namespace App\Classes;

use App\Models\StaffMember;
use Carbon\Carbon;
use App\Models\Holiday;
use App\Models\Attendance;
use App\Models\WorkDuration;
use App\Models\Leave;
use Carbon\CarbonPeriod;

class SelfCommonHrm
{
    public static function getMinutesFromTimes($startTime, $endTime)
    {
        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);
        $isNextDayForTime = self::isTimeForNextDate($startTime, $endTime);

        if ($isNextDayForTime) {
            $totalMinutes = ((24 - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  ($timeArray['end_hours'] * 60) +  $timeArray['end_minutes'];
        } else {
            $totalMinutes =  (($timeArray['end_hours'] - $timeArray['start_hours'] - 1) * 60) + (60 - $timeArray['start_minutes']) +  $timeArray['end_minutes'];
        }

        return $totalMinutes;
    }

    public static function getDetailsArrayFromTimes($startTime, $endTime)
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

    public static function isTimeForNextDate($startTime, $endTime)
    {
        $timeArray = self::getDetailsArrayFromTimes($startTime, $endTime);

        return $timeArray['start_hours'] > $timeArray['end_hours'] ? true : false;
    }

    public static function isLateClockedIn($officeStartTime, $clockInTime, $currentDate, $actualDate)
    {
        $isLate = false;
        $timeArray = self::getDetailsArrayFromTimes($officeStartTime, $clockInTime);

        if ($timeArray['end_hours'] > $timeArray['start_hours']) {
            $isLate = true;
        } else if ($timeArray['end_hours'] == $timeArray['start_hours']) {
            $isLate =  $timeArray['end_minutes'] <= $timeArray['start_minutes'] ? false : true;
        }

        // here $currendDate is the actual clocking date while $actualDate is date of attendance

        if ($currentDate && $currentDate != null) {
            $current = Carbon::parse($currentDate)->toDateString();
            $actual = Carbon::parse($actualDate)->toDateString();

            if ($current > $actual) {
                $isLate = true;
            };
        }

        return $isLate;
    }



     public static function getUserClockingTime($userId)
    {
        $company = company();

        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])
            ->where('company_id', $company->id)
            ->find($userId);

        $allIps = [];
        $allowedIpAddress = $user && $user->shift ? $user->shift->allowed_ip_address : $company->allowed_ip_address;
        if ($allowedIpAddress) {
            foreach ($allowedIpAddress as $allIpAddress) {
                $allIps[] = $allIpAddress['allowed_ip_address'];
            }
        }

        $clockInTime = $user && $user->shift ? $user->shift->clock_in_time : $company->clock_in_time;
        $clockOutTime = $user && $user->shift ? $user->shift->clock_out_time : $company->clock_out_time;
        if (!$clockInTime) {
            $clockInTime = "09:30:00";
        }
        if (!$clockOutTime) {
            $clockOutTime = "18:00:00";
        }

        $earlyClockInTime = $user && $user->shift ? $user->shift->early_clock_in_time : $company->early_clock_in_time;
        if ($earlyClockInTime) {
            $actualAllowedClockIn =  Carbon::createFromFormat('H:i:s', $clockInTime)->subMinutes($earlyClockInTime)->format('H:i:s');
        } else {
            $actualAllowedClockIn = $clockInTime;
        }
        $earlyClockOutTime = $user && $user->shift ? $user->shift->allow_clock_out_till : $company->allow_clock_out_till;
        if ($earlyClockOutTime) {
            $actualAllowedClockOut =  Carbon::createFromFormat('H:i:s', $clockOutTime)->addMinutes($earlyClockOutTime)->format('H:i:s');
        } else {
            $actualAllowedClockOut = $clockOutTime;
        }

        // If user have shift then shift time will be return
        // Else company time will be return
        return [
            'clock_in_time' => $clockInTime,
            'clock_out_time' => $clockOutTime,
            'early_clock_in_time' => $user && $user->shift ? $user->shift->early_clock_in_time : $company->early_clock_in_time,
            'allow_clock_out_till' => $user && $user->shift ? $user->shift->allow_clock_out_till : $company->allow_clock_out_till,
            'late_mark_after' => $user && $user->shift ? $user->shift->late_mark_after : $company->late_mark_after,
            'self_clocking' => $user && $user->shift ? $user->shift->self_clocking : $company->self_clocking,
            'allowed_ip_address' => $allIps,
            'actual_allowed_clock_in_time' => $actualAllowedClockIn,
            'actual_allowed_clock_out_time' => $actualAllowedClockOut,
            'is_next_day' => $user && $user->shift ? $user->shift->is_next_day : 0,
        ];
    }

    public static function getShiftTimeFromDate($date, $userId)
    {
        $company = company();
        $clockTiming = self::getUserClockingTime($userId);

        $clockInDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_in_time'], $company->timezone)
            ->setTimezone('UTC');
        $clockOutDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockTiming['clock_out_time'], $company->timezone)
            ->setTimezone('UTC');

        return [
            'clock_in_date_time' => $clockInDateTime,
            'clock_out_date_time' => $clockOutDateTime,
        ];
    }

     public static function attendanceTimingDetails ($attendance)
    {
        $company = company();

        if($attendance && $attendance->office_clock_in_time != null){

                $earlyClockInTime = $attendance && $attendance->early_clock_in_time ? $attendance->early_clock_in_time : 0;

                    if ($earlyClockInTime) {
                        $actualAllowedClockIn =  Carbon::createFromFormat('H:i:s', $attendance->office_clock_in_time)->subMinutes($earlyClockInTime)->format('H:i:s');
                    } else {
                        $actualAllowedClockIn = $attendance->office_clock_in_time;
                    }
                $earlyClockOutTime = $attendance->allow_clock_out_till ? $attendance->allow_clock_out_till :0;
                    if ($earlyClockOutTime) {
                        $actualAllowedClockOut =  Carbon::createFromFormat('H:i:s', $attendance->office_clock_out_time)->addMinutes($earlyClockOutTime)->format('H:i:s');
                    } else {
                        $actualAllowedClockOut = $attendance->office_clock_out_time;
                    }
        }else{
                 $earlyClockInTime = $company && $company->early_clock_in_time ? $company->early_clock_in_time : 0;

                    if ($earlyClockInTime) {
                        $actualAllowedClockIn =  Carbon::createFromFormat('H:i:s', $company->clock_in_time)->subMinutes($earlyClockInTime)->format('H:i:s');
                    } else {
                        $actualAllowedClockIn = $company->clock_in_time;
                    }
                $earlyClockOutTime = $company->allow_clock_out_till ? $company->allow_clock_out_till :0;
                    if ($earlyClockOutTime) {
                        $actualAllowedClockOut =  Carbon::createFromFormat('H:i:s', $company->clock_out_time)->addMinutes($earlyClockOutTime)->format('H:i:s');
                    } else {
                        $actualAllowedClockOut = $company->clock_out_time;
                    }
        }

        return [
            'actual_allowed_clock_in_time' => $actualAllowedClockIn,
            'actual_allowed_clock_out_time' => $actualAllowedClockOut,
        ];

    }


       public static function getWorkDurationMinutes($userId, $attendance, $productive = false)
    {
        $company = company();
        $productivity = 0;
        $totalBreakTime = 0;
        $averageBreak = 0;
        $breaks = 0;
        $shiftClockOutDateTime ="";
        $shiftClockOutDate = "";

        $totalWorkDurationInMinutes = 0;

        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])
            ->where('company_id', $company->id)
            ->find($userId);

        $shiftDetails = self::getUserClockingTime($userId);
    
        if ($attendance && $attendance->clock_in_time) {
                 $attendancesDetail = self::attendanceTimingDetails($attendance);
                if ($attendance->clock_out_time) {
                    $totalWorkDurationInMinutes = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNotNull('end_time')
                    ->sum('duration');
                
                // } elseif ($user && $user->shift) {
                    
                //     $attedanceClockInDateTime = Carbon::parse($attendance->clock_in_date_time)->setTimezone($company->timezone);
                    
                   
                //     $attendanceDate = Carbon::parse($attendance->date); // date only, e.g. '2025-06-03'
                //     $clockInDate = $attedanceClockInDateTime->copy()->startOfDay(); // extract Y-m-d in company timezone
                    
                //     if(strtotime($user->shift->clock_out_time) <= strtotime($user->shift->clock_in_time)){
                //         // 3. Determine which date to use for shift clock-out
                //         // if ($attendanceDate->equalTo($clockInDate)) {
                //         if ($attendanceDate->isSameDay($clockInDate)) {
                //             // Same date → shift crosses midnight → use next day for clock-out
                //             $shiftClockOutDate = $attendanceDate->copy()->addDay();
                //         } else {
                //             // Different date → use clock-in date directly
                //             $shiftClockOutDate = $clockInDate;
                //         }
                //         // dd($attendanceDate, $clockInDate, $shiftClockOutDate);
                //     }else{
                //         $shiftClockOutDate = $clockInDate;
                //     }

                    
                //     // 4. Build shift clock-out datetime using proper date
                //     $shiftClockOutDateTime = Carbon::createFromFormat(
                //         'Y-m-d H:i:s',
                //         $shiftClockOutDate->format('Y-m-d') . ' ' . $shiftDetails['actual_allowed_clock_out_time'],
                //         $company->timezone
                //     );
                //     // dd($shiftClockOutDateTime,$shiftDetails['actual_allowed_clock_out_time']);
                //     $totalWorkDurationInMinutes = WorkDuration::where('attendance_id', $attendance->id)
                //     ->where('status', 'started')
                //     ->whereNotNull('end_time')
                //     ->sum('duration');
                    
                    
                //     $lastStarted = WorkDuration::where('attendance_id', $attendance->id)
                //     ->where('status', 'started')
                //     ->whereNull('end_time')
                //     ->latest()
                //     ->first();
                    
                //     // dd($totalWorkDurationInMinutes,$lastStarted,'shift');
                //     if($lastStarted ){
                //         $now = Carbon::now($company->timezone);
                //         if ($now->gt($shiftClockOutDateTime)) {
                //             $start = Carbon::createFromFormat('Y-m-d H:i:s', $shiftClockOutDate->format('Y-m-d') . ' ' .  $lastStarted->start_time, $company->timezone);
                //             // $start->subDay();
                //             $endTime = $shiftClockOutDateTime;
                //         }else{
                //             $endTime = $now; 
                //             $start = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $lastStarted->start_time, $company->timezone);
                //         }
                //         if ($start->gt($endTime)) {
                //             $start->subDay();
                //         }
                //         // dd($start,$endTime);
                //         $duration = $start->diffInMinutes($endTime);
                //         $totalWorkDurationInMinutes += $duration;
                //     }
                 } elseif ($attendance->clock_out_time == null && $attendance->office_clock_in_time != null && $attendance->office_clock_out_time != null) {
                    
                    $attedanceClockInDateTime = Carbon::parse($attendance->clock_in_date_time)->setTimezone($company->timezone);
                    
                   
                    $attendanceDate = Carbon::parse($attendance->date); // date only, e.g. '2025-06-03'
                    $clockInDate = $attedanceClockInDateTime->copy()->startOfDay(); // extract Y-m-d in company timezone
                    
                    if(strtotime($attendance->office_clock_out_time) <= strtotime($attendance->office_clock_in_time)){
                        // 3. Determine which date to use for shift clock-out
                        // if ($attendanceDate->equalTo($clockInDate)) {
                        if ($attendanceDate->isSameDay($clockInDate)) {
                            // Same date → shift crosses midnight → use next day for clock-out
                            $shiftClockOutDate = $attendanceDate->copy()->addDay();
                        } else {
                            // Different date → use clock-in date directly
                            $shiftClockOutDate = $clockInDate;
                        }
                        // dd($attendanceDate, $clockInDate, $shiftClockOutDate);
                    }else{
                        $shiftClockOutDate = $clockInDate;
                    }
                    
                    // 4. Build shift clock-out datetime using proper date
                    $shiftClockOutDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $shiftClockOutDate->format('Y-m-d') . ' ' . $attendancesDetail['actual_allowed_clock_out_time'],
                        $company->timezone
                    );
                
                    $totalWorkDurationInMinutes = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNotNull('end_time')
                    ->sum('duration');
                    
                    
                    $lastStarted = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNull('end_time')
                    ->latest()
                    ->first();

                  
                    if($lastStarted ){
                        $now = Carbon::now($company->timezone);
                        if ($now->gt($shiftClockOutDateTime)) {
                            $start = Carbon::createFromFormat('Y-m-d H:i:s', $shiftClockOutDate->format('Y-m-d') . ' ' .  $lastStarted->start_time, $company->timezone);
                           
                            $endTime = $shiftClockOutDateTime;
                        }else{
                            $endTime = $now; 
                            $start = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $lastStarted->start_time, $company->timezone);
                        }
                        if ($start->gt($endTime)) {
                            $start->subDay();
                        }
                       
                        $duration = $start->diffInMinutes($endTime);
                        $totalWorkDurationInMinutes += $duration;
                    }
                    
                    
                } else {

                    $attedanceClockInDateTime = Carbon::parse($attendance->clock_in_date_time)->setTimezone($company->timezone);
                    
                   
                    $attendanceDate = Carbon::parse($attendance->date); // date only, e.g. '2025-06-03'
                    $clockInDate = $attedanceClockInDateTime->copy()->startOfDay(); // extract Y-m-d in company timezone
                    
                    if(strtotime($company->clock_out_time) <= strtotime($company->clock_in_time)){
                        // 3. Determine which date to use for shift clock-out
                        // if ($attendanceDate->equalTo($clockInDate)) {
                        if ($attendanceDate->isSameDay($clockInDate)) {
                            // Same date → shift crosses midnight → use next day for clock-out
                            $shiftClockOutDate = $attendanceDate->copy()->addDay();
                        } else {
                            // Different date → use clock-in date directly
                            $shiftClockOutDate = $clockInDate;
                        }
                        // dd($attendanceDate, $clockInDate, $shiftClockOutDate);
                    }else{
                        $shiftClockOutDate = $clockInDate;
                    }

                    
                    // 4. Build shift clock-out datetime using proper date
                    $shiftClockOutDateTime = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $shiftClockOutDate->format('Y-m-d') . ' ' . $attendancesDetail['actual_allowed_clock_out_time'],
                        $company->timezone
                    );
                   
                    $totalWorkDurationInMinutes = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNotNull('end_time')
                    ->sum('duration');
                    
                    
                    $lastStarted = WorkDuration::where('attendance_id', $attendance->id)
                    ->where('status', 'started')
                    ->whereNull('end_time')
                    ->latest()
                    ->first();
                    
                    // dd($totalWorkDurationInMinutes,$lastStarted,'shift');
                    if($lastStarted ){
                        $now = Carbon::now($company->timezone);
                        if ($now->gt($shiftClockOutDateTime)) {
                            $start = Carbon::createFromFormat('Y-m-d H:i:s', $shiftClockOutDate->format('Y-m-d') . ' ' .  $lastStarted->start_time, $company->timezone);
                            // $start->subDay();
                            $endTime = $shiftClockOutDateTime;
                        }else{
                            $endTime = $now; 
                            $start = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $lastStarted->start_time, $company->timezone);
                        }
                        if ($start->gt($endTime)) {
                            $start->subDay();
                        }
                        // dd($start,$endTime);
                        $duration = $start->diffInMinutes($endTime);
                        $totalWorkDurationInMinutes += $duration;
                    }

                }

                if($productive){
                    $productivity = 0;
                    $totalBreakTime = 0;
                    $averageBreak = 0;
                
                    if($attendance){
                        $shiftDuration = $attendance->shift_duration;
                        $workedDuration = $attendance->duration;
                        $productivity = round(($workedDuration/$shiftDuration * 100), 2);
                        $breaks = 0;
                        foreach($attendance->workDuration as $workDuration){
                            if($workDuration->status == 'paused'){
                               $breaks++;
                                if($workDuration->end_time != null){
                                    $totalBreakTime += $workDuration->duration;
                                }else{
                                        if($shiftClockOutDateTime && $shiftClockOutDateTime != '' )
                                        {
                                            $now = Carbon::now($company->timezone);
                                            if ($now->gt($shiftClockOutDateTime)) {
                                                $start = Carbon::createFromFormat('Y-m-d H:i:s', $shiftClockOutDate->format('Y-m-d') . ' ' .  $workDuration->start_time, $company->timezone);
                                                // $start->subDay();
                                                $endTime = $shiftClockOutDateTime;
                                            }else{
                                                $endTime = $now; 
                                                $start = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $workDuration->start_time, $company->timezone);
                                            }
                                            if ($start->gt($endTime)) {
                                                $start->subDay();
                                            }
                                            // dd($start,$endTime);
                                            $duration = $start->diffInMinutes($endTime);
                                            $workDuration->duration = $duration;
                                            $totalBreakTime += $duration;
                                        }else{
                                           
                                            $now = Carbon::now($company->timezone)->format('H:i:s');
                                            $start = Carbon::createFromFormat('H:i:s', $workDuration->start_time);
                                            $duration = $this->getMinutesFromTimes($workDuration->start_time, $now);
                                            $workDuration->duration = $duration;
                                            $totalBreakTime += $duration;
                                        }
                                }
                               }else{
                                    if($workDuration->end_time == null){
                                         if($shiftClockOutDateTime && $shiftClockOutDateTime != '' )
                                         {
                                                $now = Carbon::now($company->timezone);
                                                if ($now->gt($shiftClockOutDateTime)) {
                                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $shiftClockOutDate->format('Y-m-d') . ' ' .  $workDuration->start_time, $company->timezone);
                                                    $endTime = $shiftClockOutDateTime;
                                                }else{
                                                    $endTime = $now; 
                                                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $now->format('Y-m-d') . ' ' . $workDuration->start_time, $company->timezone);
                                                }
                                                if ($start->gt($endTime)) {
                                                    $start->subDay();
                                                }
                                                $duration = $start->diffInMinutes($endTime);
                                                $workDuration->duration = $duration;
                                          
                                         }else{

                                             $now = Carbon::now($company->timezone)->format('H:i:s');
                                             $start = Carbon::createFromFormat('H:i:s', $workDuration->start_time);
                                             $duration = $this->getMinutesFromTimes($workDuration->start_time, $now);
                                             $workDuration->duration = $duration;
                                            
                                         }
                                    }

                                }
                            
                        };
                        if($breaks == 0){
                            $averageBreak = ($totalBreakTime/1);
                        }else{
                            $averageBreak = ($totalBreakTime/$breaks);
                        }
                    } 
                }
                    
        }

        if($productive){

            return [
                        'productivity' => $productivity,
                        'total_break_time' => $totalBreakTime,
                        'avg_break' => $averageBreak,
                        'breaks' => $breaks
                    ];
        }else{
            return $totalWorkDurationInMinutes;
        }

    }

    public static function attendanceDetailByDate($type = false)
    {   
        
        $request = request();
        $company = company();

        $loggedUser = user();
        $statusDate = $request->status_date ?? [];
        $startDate = "";
        $endDate = "";

        // add shift wise date not from request

        $shiftClockInTime = self::getUserClockingTime($loggedUser->id);

        $lastAttendance = Attendance::where('user_id', $loggedUser->id)
            // ->whereNotNull('clock_in_time')
            ->orderByDesc('date')
            ->first();

        $effectiveGap = 0;
        $currentDateTime = Carbon::now($company->timezone);
        $currentDate = $currentDateTime->toDateString();

        if($shiftClockInTime){

            $totalMinutesOfShift = self::getMinutesFromTimes(
                        $shiftClockInTime['clock_in_time'],
                        $shiftClockInTime['clock_out_time']
                    );

             
            $actual = Carbon::now($company->timezone);
                   
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $actual->format('Y-m-d') . ' ' . $shiftClockInTime['actual_allowed_clock_out_time'], $company->timezone);
            $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $actual->format('Y-m-d') . ' ' . $shiftClockInTime['actual_allowed_clock_in_time'], $company->timezone);
                    

            if ( strtotime($shiftClockInTime['clock_out_time']) <= strtotime($shiftClockInTime['clock_in_time'])) {
                
                $actualClockingTime = Carbon::now($company->timezone)->format('H:i:s');
               
                if (($endTime->gt($actual)) && 1440 > $totalMinutesOfShift) {
                    $currentDateTimeObject = Carbon::now($company->timezone)->subDay();
                    //  dd( $actual, $currentDateTimeObject);
                } else if (1440 <= $totalMinutesOfShift) {
                    if ($lastAttendance) {
                        $lastClockInDate = Carbon::parse(
                            $lastAttendance->date->format('Y-m-d'),
                            $company->timezone
                        )->toDateString();
                
                        // Step 1: Get date range
                        $dateStrings = array_map(fn($d) => $d->toDateString(), iterator_to_array(CarbonPeriod::create($lastClockInDate, $currentDate)));
        
    
                        // Step 2: Get holidays and leave dates
                        $holidays = Holiday::whereBetween('date', [$lastClockInDate, $currentDate])->pluck('date')->map->toDateString()->all();
                       
                        $leaves = Leave::where('status', 'approved')
                            ->where('user_id', $loggedUser->id)
                            ->whereDate('start_date', '<=', $currentDate)
                            ->whereDate('end_date', '>=', $lastClockInDate)
                            ->get();
                      
    
                        $leaveDates = collect();
                        foreach ($leaves as $leave) {
                            $period = Carbon::parse($leave->start_date)->toPeriod($leave->end_date);
                            $leaveDates = $leaveDates->merge(collect($period)->map(fn($date) => $date->toDateString()));
                        }
    
                        // Step 3: Calculate effective gap
                        $gapInDays = Carbon::parse($lastClockInDate)->diffInDays($currentDate);
                       
                        $effectiveGap = $gapInDays - count(array_intersect($dateStrings, array_unique(array_merge($holidays, $leaveDates->all()))));
                        // Step 4: Adjust datetime
                      
                        if ($effectiveGap < 1) {
    
                            if (strtotime($actualClockingTime) < strtotime($shiftClockInTime['actual_allowed_clock_in_time'])) {
    
                                $currentDateTimeObject = $currentDateTime->copy()->subDay();
                            } else {
                                $currentDateTimeObject = $currentDateTime;
                            }
                        } else if (strtotime($actualClockingTime) < strtotime($shiftClockInTime['actual_allowed_clock_out_time']) && $effectiveGap >= 1) {
                            //if a person comes after midnight (after 00:00:00:am, next day) then we consider the perious date
                            $currentDateTimeObject = $currentDateTime->copy()->subDay();
                        } else {
                        
                            $currentDateTimeObject = $currentDateTime;
                        }
                    }
                } else {
                    $currentDateTimeObject = Carbon::now($company->timezone);
                }
            } else {
                $currentDateTimeObject = Carbon::now($company->timezone);
            }

             $startDate = $currentDateTimeObject->copy()->format('Y-m-d');
             $endDate = $currentDateTimeObject->copy()->format('Y-m-d');
        }else if ($request->has('status_date') && is_array($request->status_date)) {
            $startDate = Carbon::parse($statusDate[0]);
            $endDate = Carbon::parse($statusDate[1]);
        }
      
        $userId = $loggedUser->id;
        if($type == true){
            $resultData = Attendance::whereDate('attendances.date', $startDate)->where('user_id', $userId)->with(['user','user.shift', 'leave', 'leaveType', 'holiday','workDuration'])->get();
        }else{
            $resultData = self::getAttendanceDetails($userId, $startDate, $endDate);
        }

        return $resultData;
    }

    public static function getAttendanceDetails($userId, $startDate, $endDate)
    {
        $company = company();
        $user = StaffMember::select('id', 'name', 'shift_id')->with(['shift'])->find($userId);

        $currentDateTime = Carbon::now($company->timezone);
        $today = Carbon::now()->setTimezone($company->timezone)->startOfDay();
        $startDate = Carbon::parse($startDate)->setTimezone($company->timezone)->startOfDay();
        $endDate = Carbon::parse($endDate)->setTimezone($company->timezone)->startOfDay();

        $attendanceData = [];
        $clockedInDuration = 0;
        $totalLateDays = 0;
        $totalHalfDays = 0;
        $totalPaidLeave = 0;
        $paidLeaveCount = 0;
        $totalUnPaidLeave = 0;
        $totalHolidayCount = 0;
        $totalDays = 0;

        $shiftDetails = self::getUserClockingTime($userId);

        $shiftClockInTime = $shiftDetails['clock_in_time'];
        $shiftClockOutTime = $shiftDetails['clock_out_time'];
        $officeHoursInMinutes = self::getMinutesFromTimes($shiftClockInTime, $shiftClockOutTime);

        $allAttendances = Attendance::with(['leave:id,leave_type_id,reason', 'leave.leaveType:id,name', 'holiday'])
            ->whereBetween('attendances.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('attendances.user_id', $userId)
            ->get();
        // dd($allAttendances, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $allHolidays = Holiday::whereBetween('holidays.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $totalWorkingDays = 0;
        $totalPresentDays = 0;
        $total_office_time =0;

        while ($endDate->gte($startDate)) {
            $currentDate = $endDate->copy();
            $isAttendanceDataFound = $allAttendances->filter(function ($allAttendance) use ($currentDate) {
                return $allAttendance->date->format('Y-m-d') == $currentDate->format('Y-m-d');
            })->first();

            $isHolidayDataFound = $allHolidays->filter(function ($allHoliday) use ($currentDate) {
                return $allHoliday->date->format('Y-m-d') == $currentDate->format('Y-m-d');
            })->first();

            $totalWorkDurationInMinutes = 0;
            $office_time = 0;
            $isHoliday = false;
            $isLeave = false;
            $isHalfHoliday = false;
            $holidayName = '';
            $leaveName = '';
            $leaveReason = '';
            $totalLateTime = 0;
            $lateTime = 0;

            // if ($isHolidayDataFound && $isHolidayDataFound->is_half_day == 1) {
            //     $status = 'half_holiday';
            //     $isHoliday = true;
            //     $isHalfHoliday = true;
            //     $holidayName = $isHolidayDataFound->name;
            //     $totalHolidayCount += 0.5;
            // }

             if ($isHolidayDataFound && $isHolidayDataFound->is_half_day == 1) {
                $isHalfHoliday = true;
                $holidayName = $isHolidayDataFound->name;
            }


            if ($currentDate->gt($today)) {
                $status = 'upcoming';
            } else if ($isAttendanceDataFound) {
                if ($isAttendanceDataFound->leave_type_id && $isAttendanceDataFound->is_half_day) {
                    $status = 'half_day';
                    $isLeave = true;
                    $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                    $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';

                    $totalPresentDays += 0.5;
                    $totalHalfDays += 1;
                } else if ($isAttendanceDataFound->leave_type_id) {
                    $status = 'absent';
                    $isLeave = true;
                    $leaveName = $isAttendanceDataFound->leave && $isAttendanceDataFound->leave->leaveType ? $isAttendanceDataFound->leave->leaveType->name : '';
                    $leaveReason = $isAttendanceDataFound->leave ? $isAttendanceDataFound->leave->reason : '';
                } else if ($isHalfHoliday) {
                    $totalPresentDays += 1;
                } else {
                    $status = 'present';

                    $totalPresentDays += 1;
                }

                if ($status == 'half_day' || $status == 'present' || $status == 'half_holiday') {
                    $totalWorkDurationInMinutes =  self::getWorkDurationMinutes($userId,$isAttendanceDataFound);
                     $office_time = $isAttendanceDataFound->office_clock_in_time != null?self::getMinutesFromTimes($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->office_clock_out_time):$officeHoursInMinutes;
           
                    if ($isAttendanceDataFound->is_late) {
                        $isLateClockedIn = CommonHrm::isLateClockedIn($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time, $isAttendanceDataFound->actual_clock_in_date, $isAttendanceDataFound->date);

                        if ($isLateClockedIn) {
                            $lateTime =  CommonHrm::getMinutesFromTimes($isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time);
                            // dd($lateTime,$isAttendanceDataFound->office_clock_in_time, $isAttendanceDataFound->clock_in_time, $lateTime);
                        } else {
                            $lateTime = 0;
                        }
                    } else {
                        $lateTime = 0;
                    }
                }

                
                if ($isAttendanceDataFound->is_paid) {
                    $totalPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;

                    if ($isAttendanceDataFound->leave_type_id) {
                        $paidLeaveCount += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                    }
                } else {
                    $totalUnPaidLeave += $isAttendanceDataFound->is_half_day ? 0.5 : 1;
                }

                 $totalWorkingDays += $isHalfHoliday ? 0.5 : 1;
                 $total_office_time += $isHalfHoliday ? (0.5* $office_time) : (1*$office_time);
            } else if ($isHolidayDataFound && !$isAttendanceDataFound) {
                if ($isHalfHoliday) {
                    $status = 'half_holiday';
                    $isHoliday = true;
                    $totalHolidayCount += 0.5;
                    $totalWorkingDays += 0.5;
                    $total_office_time += (0.5* $office_time);
                } else {
                    $status = 'holiday';
                    $isHoliday = true;
                    $totalHolidayCount += 1;
                    $totalWorkingDays += 1;
                    $total_office_time += (1*$office_time);
                }
            
            } else {
                $status = 'not_marked';
                $totalWorkingDays += 1;
                $total_office_time += (1*$office_time);
            }

            if ($status != 'upcoming') {
                $isUserLate = $isAttendanceDataFound && $isAttendanceDataFound->is_late ? $isAttendanceDataFound->is_late : 0;
                $productivities =  self::getWorkDurationMinutes($userId,$isAttendanceDataFound, $productive = true);
                $attendanceData[] = [
                    'attendance' => $isAttendanceDataFound,
                    'productivity' => $productivities['productivity'],
                    'total_break_time' => $productivities['total_break_time'],
                    'avg_break' => $productivities['avg_break'],
                    'breaks' => $productivities['breaks'],
                    'date' => $currentDate->format('Y-m-d'),
                    'status' => $status,
                    'is_holiday' => $isHoliday,
                    'holiday_name' => $holidayName,
                    'is_leave' => $isLeave,
                    'leave_name' => $leaveName,
                    'is_late'   => $isUserLate,
                    'late_time' => $lateTime,
                    'clock_in'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_in_date_time : '',
                    'clock_out'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_out_date_time : '',
                    'clock_in_ip'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_in_ip_address : '',
                    'clock_out_ip'  => $isAttendanceDataFound ? $isAttendanceDataFound->clock_out_ip_address : '',
                    'leave_reason'  => $leaveReason,
                    'worked_time'  => $totalWorkDurationInMinutes,
                    'office_time' => $office_time,
                ];

                $totalLateDays += $isUserLate;
                $clockedInDuration += $totalWorkDurationInMinutes;
            }

            $totalDays += 1;

            $endDate->subDay();
            $totalLateTime = collect($attendanceData)->sum('late_time');
        }

        return [
            'data'  => $attendanceData,
            'total_days'  => $totalDays,
            'working_days'  => $totalWorkingDays,
            'present_days'  => $totalPresentDays,
            'paid_leaves'  => $paidLeaveCount,
            'half_days'  => $totalHalfDays,
            'office_time'  => $officeHoursInMinutes,
            // 'total_office_time'  => $totalWorkingDays * $officeHoursInMinutes,
            'total_office_time' => $total_office_time,
            'clock_in_duration'  => $clockedInDuration,
            'total_late_days'  => $totalLateDays,
            'total_paid_leaves' => $totalPaidLeave,
            'total_unpaid_leaves' => $totalUnPaidLeave,
            'holiday_count' => $totalHolidayCount,
            'total_late_time' => $totalLateTime
        ];
    }
}

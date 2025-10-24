<?php

namespace Database\Seeders;

use App\Classes\CommonHrm;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\User;
use App\Observers\AttendanceObserver;
use App\Observers\LeaveObserver;
use App\Observers\LeaveTypeObserver;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        DB::table('leave_types')->delete();
        DB::table('leaves')->delete();
        DB::table('attendances')->delete();

        DB::statement('ALTER TABLE leave_types AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE leaves AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE attendances AUTO_INCREMENT = 1');

        $company = Company::with(['currency' => function ($query) {
            return $query->withoutGlobalScope(CompanyScope::class);
        }, 'subscriptionPlan'])->where('is_global', 0)->first();
        session(['company' => $company]);

        // Manually Dispatch Events
        LeaveType::observe(LeaveTypeObserver::class);
        Leave::observe(LeaveObserver::class);
        Attendance::observe(AttendanceObserver::class);

        $admin = User::where('company_id', $company->id)->where('name', 'Admin')->where('status', 'active')->first();

        DB::table('leave_types')->insert([
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Sick',
                'is_paid' => true,
                'total_leaves' => 5,
                'max_leaves_per_month' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Paid',
                'is_paid' => true,
                'total_leaves' => 10,
                'max_leaves_per_month' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $company->id,
                'created_by' => $admin->id,
                'name' => 'Unpaid',
                'is_paid' => false,
                'total_leaves' => 30,
                'max_leaves_per_month' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $leaveTypes = DB::table('leave_types')->where('company_id', $company->id)->pluck('id')->toArray();
        $alUsers = User::with(['shift'])->where('company_id', $company->id)
            ->where('status', 'active')->get();
        $leaveTypes = DB::table('leave_types')->where('company_id', $company->id)->pluck('id')->toArray();


        $firstLeaveDay = fake()->numberBetween(120, 140);

        $allDates = CommonHrm::generateDatesFromToday($firstLeaveDay);

        $startDate = $allDates[0]; // Oldest date (first element)
        $endDate = end($allDates); // Newest date (last element)

        $allHolidays = Holiday::where('company_id', $company->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        foreach ($allDates as $allDate) {
            $isHoliday = in_array($allDate, $allHolidays);

            if (!$isHoliday) {
                foreach ($alUsers as $allUser) {
                    $isLeave = fake()->randomElement([0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0]);

                    $officeClockInTime = $allUser && $allUser->shift ? $allUser->shift->clock_in_time : $company->clock_in_time;
                    $officeClockOutTime = $allUser && $allUser->shift ? $allUser->shift->clock_out_time : $company->clock_out_time;
                    $officeEarlyClockIn = $allUser && $allUser->shift ? $allUser->shift->early_clock_in_time : $company->early_clock_in_time;
                    $officeAllowedClockOutTime = $allUser && $allUser->shift ? $allUser->shift->allow_clock_out_till : $company->allow_clock_out_till;
                    $lateMarkAfter = $allUser && $allUser->late_mark_after ? $allUser->shift->late_mark_after : $company->late_mark_after;

                    if ($isLeave) {
                        $leave = new Leave();
                        $leave->company_id = $company->id;
                        $leave->user_id = $allUser->id;
                        $leave->leave_type_id = fake()->randomElement($leaveTypes);
                        $leave->start_date = $allDate;
                        $leave->end_date = $allDate;
                        $leave->total_days = 1;
                        $leave->reason = fake()->text();
                        $leave->is_half_day = false;
                        $leave->half_leave = 'before_break';
                        $leave->status = fake()->randomElement(['pending', 'pending', 'approved', 'approved', 'approved', 'rejected']);
                        $leave->save();

                        if ($leave->status == 'approved') {
                            CommonHrm::updateLeaveStatus($leave->id);
                        }
                    } else {
                        $randomClockInMinutes = 0;
                        $randomClockInMinutes = fake()->numberBetween(0, 40);
                        $randomClockOutMinutes = fake()->numberBetween(0, 30);
                        $clockInTime = $this->generateRandomTimeWithOffset($officeClockInTime, $randomClockInMinutes);
                        $clockOutTime = $this->generateRandomTimeWithOffset($officeClockOutTime, $randomClockOutMinutes);

                        $clockInDateTime = Carbon::parse($allDate . $clockInTime, $company->timezone)->timezone('UTC');
                        $clockOutDateTime = Carbon::parse($allDate . $clockOutTime, $company->timezone)->timezone('UTC');

                        $attendance = new Attendance();
                        $attendance->company_id = $company->id;
                        $attendance->date = $allDate;
                        $attendance->actual_clock_in_date = $allDate;
                        $attendance->is_holiday = 0;
                        $attendance->is_leave = 0;
                        $attendance->user_id = $allUser->id;
                        $attendance->clock_in_date_time = $clockInDateTime;
                        $attendance->clock_out_date_time = $clockOutDateTime;
                        $attendance->clock_in_ip_address = fake()->ipv4();
                        $attendance->total_duration = self::getMinutesDifference($clockInDateTime, $clockOutDateTime);
                        $attendance->clock_in_time = $clockInTime;
                        $attendance->clock_out_time = $clockOutTime;
                        $attendance->office_clock_in_time = $officeClockInTime;
                        $attendance->office_clock_out_time = $officeClockOutTime;
                        $attendance->early_clock_in_time = $officeEarlyClockIn;
                        $attendance->allow_clock_out_till = $officeAllowedClockOutTime;
                        $attendance->is_paid = 1;
                        $attendance->is_late = $randomClockInMinutes > $lateMarkAfter;
                        $attendance->late_time_duration = ($randomClockInMinutes > $lateMarkAfter) ? CommonHrm::getMinutesFromTimes($officeClockInTime, $clockInTime):0;
                        $attendance->status = 'present';
                        $attendance->save();

                        $numberOfDurations = fake()->numberBetween(2, 4);
                        $workDurations = [];

                        $date = $attendance->date;
                        
                        $localDate = $date->copy()->setTimezone($company->timezone);
                        $dateOfAttendance = $localDate->toDateString();
                        $startTime = Carbon::parse("{$dateOfAttendance} {$attendance->clock_in_time}", $company->timezone);
                        if(strtotime($officeClockInTime) > strtotime($officeClockOutTime))
                        {
                            $localDate->addDay();
                            $dateOfAttendance = $localDate->toDateString();
                        }
                        $finalEndTime = Carbon::parse("{$dateOfAttendance} {$attendance->clock_out_time}", $company->timezone);
                        // Track remaining time
                        $totalAvailableMinutes = $startTime->diffInMinutes($finalEndTime);
                        $remainingMinutes = $totalAvailableMinutes;

                        $pauseNotes = [
                                            'For medical emergency',
                                            'Tea break',
                                            'Urgent work at home',
                                            'Lunch break',
                                            'I am not feeling well'
                                     ];

                       $nextStatus = 'started'; // always begin with 'started'

                        for ($i = 0; $i < $numberOfDurations; $i++) {
                            // If last chunk, consume the remaining time
                            if ($i === $numberOfDurations - 1) {
                                $durationMinutes = $remainingMinutes;
                            } else {
                                // Leave enough time for remaining chunks
                                $maxPossible = $remainingMinutes - (($numberOfDurations - $i - 1) * 15);
                                $durationMinutes = fake()->numberBetween(15, min(120, $maxPossible));
                            }

                            $endTime = $startTime->copy()->addMinutes($durationMinutes);

                            // Set note only if paused
                            $note = $nextStatus === 'paused' ? fake()->randomElement($pauseNotes) : null;

                            $workDurations[] = [
                                'company_id'    => $company->id,
                                'attendance_id' => $attendance->id,
                                'start_time'    => $startTime->copy(),
                                'end_time'      => $endTime->copy(),
                                'duration'      => $durationMinutes,
                                'status'        => $nextStatus,
                                'notes'         => $note,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ];

                            $startTime = $endTime->copy();
                            $remainingMinutes -= $durationMinutes;

                            // Alternate status
                            $nextStatus = $nextStatus === 'started' ? 'paused' : 'started';
                        }

                        DB::table('work_durations')->insert($workDurations);
                    }
                }
            }
        }
    }

    function generateRandomTimeWithOffset($time, $maxMinutesToAdd)
    {
        // Generate a random number of minutes to add (between 0 and $maxMinutesToAdd)
        $randomMinutes = rand(0, $maxMinutesToAdd);

        // Convert given time to Carbon instance and add random minutes
        return Carbon::parse($time)->addMinutes($randomMinutes)->format('H:i:s');
    }

    function getMinutesDifference($startDateTime, $endDateTime)
    {
        $start = Carbon::parse($startDateTime);
        $end = Carbon::parse($endDateTime);

        return $start->diffInMinutes($end);
    }
}

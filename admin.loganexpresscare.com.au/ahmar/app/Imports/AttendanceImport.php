<?php

namespace App\Imports;

use App\Classes\CommonHrm;
use App\Models\Attendance;
use App\Models\Role;
use App\Models\StaffMember;
use Carbon\Carbon;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class AttendanceImport implements ToArray, WithHeadingRow
{
    protected $override;
    protected $company;

    public function __construct($override = false, $company = null)
    {
        $this->override = $override;
        $this->company = $company;
    }

    public function array(array $records)
    {
        DB::transaction(function () use ($records) {
            foreach ($records as $row) {
                if (
                    !array_key_exists('employee_number', $row) ||
                    !array_key_exists('date', $row) ||
                    !array_key_exists('clock_in_time', $row) ||
                    !array_key_exists('clock_out_time', $row)
                ) {
                    throw new ApiException('Missing required fields: employee_number, date, clock_in_time, clock_out_time.');
                }

                $employeeId = trim($row['employee_number']);
                $user = StaffMember::where('employee_number', $employeeId)->first();

                if (!$user) {
                    throw new ApiException("No user found with employee_number: $employeeId");
                }

                $date = trim($row['date']);
                $clockInTime = trim($row['clock_in_time']);
                $clockOutTime = trim($row['clock_out_time']);

                $attendance = Attendance::where('user_id', $user->id)
                    ->where('date', $date)
                    ->first();

                $isUpdate = false;

                if ($attendance) {
                    if (!$this->override) {
                        continue; // Skip if attendance already exists and override is false
                    }
                    $isUpdate = true; // Existing attendance being updated
                } else {
                    $attendance = new Attendance();
                    $attendance->user_id = $user->id;
                    $attendance->date = $date;
                }

                $attendance->clock_in_time = $clockInTime;
                $attendance->clock_out_time = $clockOutTime;

                $userClockIn = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockInTime, $this->company->timezone);
                $userClockOut = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $clockOutTime, $this->company->timezone);

                if ($userClockOut->lt($userClockIn)) {
                    $userClockOut->addDay(); // Handle overnight
                }

                $attendance->clock_in_date_time = $userClockIn->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $attendance->clock_out_date_time = $userClockOut->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
                $attendance->total_duration = $userClockIn->diffInMinutes($userClockOut);

                $attendance->save();

                // Work Duration logic
                if ($attendance->clock_in_time !== null) {
                    $totalDuration = $attendance->clock_out_time
                        ? CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->clock_out_time)
                        : CommonHrm::getMinutesFromTimes($attendance->clock_in_time, $attendance->office_clock_out_time);

                    if ($isUpdate && $this->override) {
                        // Update existing work_duration record
                        DB::table('work_durations')
                            ->where('attendance_id', $attendance->id)
                            ->update([
                                'start_time' => $attendance->clock_in_time,
                                'end_time'   => $attendance->clock_out_time ?? $attendance->office_clock_out_time,
                                'duration'   => $totalDuration,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Insert new work_duration record
                        DB::table('work_durations')->insert([
                            'company_id'    => $attendance->company_id,
                            'attendance_id' => $attendance->id,
                            'start_time'    => $attendance->clock_in_time,
                            'end_time'      => $attendance->clock_out_time ?? $attendance->office_clock_out_time,
                            'duration'      => $totalDuration,
                            'status'        => 'started',
                            'notes'         => null,
                            'created_at'    => $attendance->created_at,
                            'updated_at'    => $attendance->updated_at,
                        ]);
                    }
                }
            }
        });
    }
}

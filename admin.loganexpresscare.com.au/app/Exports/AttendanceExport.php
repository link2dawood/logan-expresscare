<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection,  WithHeadings
{
    use Exportable;

    private $attendances;

    public function __construct($attendanceDetails)
    {
        $this->attendances = collect($attendanceDetails);
    }

    public function headings(): array
    {
        return [
            'name' => 'Employee Name',
            'employee_number' => 'Employee ID',
            'date' => 'Date',
            'clock_in_time' => 'Clock In Time',
            'clock_out_time' => 'Clock Out Time',
            'status' => 'Status',
        ];
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            if (!empty($attendance['is_half_day'])) {
                $status = 'Half Day';
            } elseif (!empty($attendance['x_leave_type_id'])) {
                $status = 'On Leave';
            } elseif (!empty($attendance['clock_in_date_time']) && !empty($attendance['clock_out_date_time'])) {
                $status = 'Present';
            } else {
                $status = 'Absent';
            }

            return [
                'name' => $attendance['user']['name'] ?? '',
                'employee_number' => $attendance['user']['employee_number'] ?? '',
                'date' => $attendance['date'] ?? '',
                'clock_in_time' => $attendance['clock_in_time'] ?? '',
                'clock_out_time' => $attendance['clock_out_time'] ?? '',
                'status' => $status,
            ];
        });
    }
}

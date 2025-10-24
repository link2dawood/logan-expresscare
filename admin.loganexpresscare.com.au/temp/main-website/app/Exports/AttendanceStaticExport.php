<?php

namespace App\Exports;

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;

class AttendanceStaticExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $attendances;

    public function __construct(array $attendanceDetails)
    {
        $this->attendances = collect($attendanceDetails);
    }

    public function headings(): array
    {
        return [
            'employee_number',
            'date',
            'clock_in_time',
            'clock_out_time',
            'status',
        ];
    }

    public function collection(): Collection
    {
        return $this->attendances->map(function ($attendance) {
            return [
                'employee_number' => $attendance['employee_number'] ?? '',
                'date'            => $attendance['date'] ?? '',
                'clock_in_time'   => $attendance['clock_in_time'] ?? '',
                'clock_out_time'  => $attendance['clock_out_time'] ?? '',
                'status'          => $attendance['status'] ?? 'Present',
            ];
        });
    }
}

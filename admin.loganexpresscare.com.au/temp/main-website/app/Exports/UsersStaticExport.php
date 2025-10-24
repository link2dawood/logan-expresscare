<?php

namespace App\Exports;

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;

class UsersStaticExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $users;

    public function __construct(array $userDetails)
    {
        $this->users = collect($userDetails ?? []);
    }

    public function headings(): array
    {
        return [
            'name',
            'employee_number',
            'email',
            'phone',
            'allow_login',
            'status',
            'gender',
            'joining_date',
            'password',
        ];
    }

    public function collection(): Collection
    {
        return $this->users->map(function ($user) {
            return [
                'name'            => $user['name'] ?? '',
                'employee_number' => $user['employee_number'] ?? '',
                'email'           => $user['email'] ?? '',
                'phone'           => $user['phone'] ?? '',
                'allow_login'     => $user['allow_login'] ?? '',
                'status'          => $user['status'] ?? '',
                'gender'          => $user['gender'] ?? '',
                'joining_date'    => $user['joining_date'] ?? '',
                'password'       => $user['password'] ?? '',
            ];
        });
    }
}

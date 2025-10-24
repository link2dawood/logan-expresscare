<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Shift extends BaseModel
{
    protected $table = 'shifts';

    protected $default = ['xid', 'name', 'clock_in_time', 'clock_out_time', 'late_mark_after', 'early_clock_in_time', 'allow_clock_out_till', 'self_clocking', 'allowed_ip_address', 'is_next_day'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id',];

    protected $appends = ['xid', 'employee_count'];

    protected $filterable = ['name'];

    protected $casts = [
        'allowed_ip_address' => 'json',
        'late_mark_after' => 'integer',
        'self_clocking' => 'integer',
        'early_clock_in_time' => 'integer',
        'allow_clock_out_till' => 'integer',
        'capture_location' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getEmployeeCountAttribute()
    {
        $employeeCount = StaffMember::where('shift_id', $this->id)
            ->count();

        return [
            'employee_count' => $employeeCount,
        ];
    }
}

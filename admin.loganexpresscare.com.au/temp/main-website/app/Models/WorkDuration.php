<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class WorkDuration extends BaseModel
{
    protected $table = 'work_durations';

    protected $default = ['xid', 'x_attendance_id'];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $hidden = ['id', 'attendance_id'];

    protected $appends = ['xid', 'x_attendance_id'];

    protected $filterable = ['status'];

    protected $hashableGetterFunctions = [
        'getXAttendanceIdAttribute' => 'attendance_id',
    ];

    protected $casts = ['attendance_id' => Hash::class . ':hash',];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\LeaveType;

class Leave extends BaseModel
{
    protected $table = 'leaves';

    protected $default = ['xid', 'start_date', 'end_date', 'is_half_day', 'is_paid', 'reason', 'status', 'half_leave','total_days'];

    protected $guarded = ['id', 'status', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'leave_type_id'];

    protected $appends = ['xid', 'x_user_id', 'x_leave_type_id'];

    protected $filterable = ['status'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXLeaveTypeIdAttribute' => 'leave_type_id',
    ];

    protected $casts = [
        'is_paid' => 'integer',
        'is_half_day' => 'integer',
        'user_id' => Hash::class . ':hash',
        'leave_type_id' => Hash::class . ':hash',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function leaveType()
    {
        return $this->hasOne(LeaveType::class, 'id', 'leave_type_id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }
}

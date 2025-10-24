<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\LeaveType;

class EmployeeSpecificLeaveCount extends BaseModel
{
    protected $table = 'employee_specific_leave_counts';

    protected $default = ['xid', 'x_user_id', 'x_leave_type_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'leave_type_id'];

    protected $appends = ['xid', 'x_user_id', 'x_leave_type_id'];

    protected $filterable = ['status'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXLeaveTypeIdAttribute' => 'leave_type_id',
    ];

    protected $casts = [
        'total_leaves' => 'integer',
        'user_id' => Hash::class . ':hash',
        'leave_type_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class SalaryGroupUser extends BaseModel
{
    protected $table = 'salary_group_users';

    protected $default = ['xid', 'x_user_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'salary_group_id', 'user_id'];

    protected $appends = ['xid', 'x_salary_group_id', 'x_user_id'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    protected $hashableGetterFunctions = [
        'getXSalaryGroupIdAttribute' => 'salary_group_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'salary_group_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
    ];

    public function salaryGroup()
    {
        return $this->belongsTo(SalaryGroup::class, 'salary_group_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }
}

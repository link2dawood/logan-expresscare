<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Payroll extends BaseModel
{
    protected $table = 'payrolls';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'created_by', 'updated_by', 'account_id'];

    protected $appends = ['xid', 'x_user_id', 'x_created_by', 'x_updated_by', 'x_account_id'];

    protected $filterable = ['user_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXAccountIdAttribute' => 'account_id',
        'getXCreatedByAttribute' => 'created_by',
        'getXUpdatedByAttribute' => 'updated_by',
    ];

    protected $casts = [
        'account_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'created_by' => Hash::class . ':hash',
        'updated_by' => Hash::class . ':hash',
        'payment_date' => 'date',
        'salary_amount' => 'double',
        'pre_payment_amount' => 'double',
        'net_salary' => 'double',
        'total_office_time' => 'integer',
        'total_worked_time' => 'integer',
        'half_days' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function payrollComponents()
    {
        return $this->hasMany(PayrollComponent::class, 'payroll_id', 'id');
    }
}

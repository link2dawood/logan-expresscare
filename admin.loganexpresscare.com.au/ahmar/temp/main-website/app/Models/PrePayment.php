<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class PrePayment extends BaseModel
{
    protected $table = 'pre_payments';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'account_id'];

    protected $appends = ['xid', 'x_user_id', 'x_account_id'];

    protected $filterable = ['id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXAccountIdAttribute' => 'account_id',
    ];

    protected $permissions = ['account_view'];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'account_id' => Hash::class . ':hash',
        'amount' => 'double',
        'payroll_month' => 'integer',
        'payroll_year' => 'integer',
        'deduct_from_payroll' => 'integer',
        'date_time' => 'datetime',
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

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}

<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Account extends BaseModel
{
    protected $table = 'accounts';

    protected $default = ['xid', 'name', 'account_number', 'branch_code', 'branch_address'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id'];

    protected $appends = ['xid', 'x_company_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
    ];

    protected $casts = [
        'initial_balance' => 'double',
        'balance' => 'double',
    ];

    protected $permissions = ['accounts_view'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

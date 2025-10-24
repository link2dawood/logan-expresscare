<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class DepositCategory extends BaseModel
{
    protected $table = 'deposit_categories';

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id'];

    protected $appends = ['xid', 'x_company_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

}

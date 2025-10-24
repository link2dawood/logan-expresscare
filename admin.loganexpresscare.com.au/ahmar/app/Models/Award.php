<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Award extends BaseModel
{
    protected $table = 'awards';

    protected $default = ['xid', 'name', 'active', 'description', 'award_price'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'created_by'];

    protected $appends = ['xid', 'x_company_id', 'x_created_by'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXCreatedByAttribute' => 'created_by',
    ];

    protected $casts = [
        'active' => 'integer',
        'award_price' => 'double'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

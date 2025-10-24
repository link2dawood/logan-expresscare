<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Location extends BaseModel
{
    protected $table = 'locations';

    protected $default = ['xid', 'name', 'address'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

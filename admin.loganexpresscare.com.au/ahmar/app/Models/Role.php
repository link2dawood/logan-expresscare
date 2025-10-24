<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Traits\EntrustRoleTrait;

class Role extends BaseModel
{
    use EntrustRoleTrait;

    protected  $table = 'roles';

    protected $default = ['xid', 'id', 'name', 'display_name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

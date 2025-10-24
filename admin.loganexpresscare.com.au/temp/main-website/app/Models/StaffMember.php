<?php

namespace App\Models;

use App\Casts\Hash;
use App\Scopes\CompanyScope;

class StaffMember extends User
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

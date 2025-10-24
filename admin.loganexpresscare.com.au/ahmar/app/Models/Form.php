<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Form extends BaseModel
{
    protected $table = 'forms';

    protected $default = ['xid', 'name', 'form_fields'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    protected $casts = [
        'form_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

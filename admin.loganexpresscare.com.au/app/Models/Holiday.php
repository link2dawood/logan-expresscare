<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Holiday extends BaseModel
{
    protected $table = 'holidays';

    protected $default = ['xid', 'name', 'year', 'date', 'created_by', 'month', 'is_half_day', 'half_holiday'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name', 'year', 'month', 'is_weekend'];

    protected $casts = [
        'is_deletable' => 'integer',
        'date'  => 'date:Y-m-d',
        'is_half_day' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Classes\Common;

class EmployeeFields extends BaseModel
{
    protected $table = 'employee_fields';

    protected $default = ['xid', 'name', 'type', 'is_required', 'default_value'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'field_type_id'];

    protected $appends = ['xid', 'x_company_id', 'x_field_type_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXFieldTypeIdAttribute' => 'field_type_id',
    ];

    protected $casts = [
        'is_required' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}

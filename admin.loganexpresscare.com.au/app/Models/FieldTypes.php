<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class FieldTypes extends BaseModel
{
    protected $table = 'field_types';

    protected $default = ['xid', 'name', 'visible_to_employee'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id'];

    protected $appends = ['xid', 'x_company_id'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
    ];

    protected $casts = [
        'visible_to_employee' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function employeeField()
    {
        return $this->hasMany(EmployeeFields::class, 'field_type_id', 'id')
            ->orderBy('id', 'asc');
    }
}

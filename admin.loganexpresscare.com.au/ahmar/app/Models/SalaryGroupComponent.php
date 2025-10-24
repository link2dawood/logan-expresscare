<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class SalaryGroupComponent extends BaseModel
{
    protected $table = 'salary_group_components';

    protected $default = ['xid', 'x_salary_component_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'salary_group_id', 'salary_component_id'];

    protected $appends = ['xid', 'x_salary_component_id', 'x_salary_group_id'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    protected $hashableGetterFunctions = [
        'getXSalaryComponentIdAttribute' => 'salary_component_id',
        'getXSalaryGroupIdAttribute' => 'salary_group_id',
    ];

    protected $casts = [
        'salary_component_id' => Hash::class . ':hash',
        'salary_group_id' => Hash::class . ':hash',
    ];

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class, 'salary_component_id', 'id');
    }
    public function salaryGroup()
    {
        return $this->belongsTo(SalaryGroup::class, 'salary_group_id', 'id');
    }
}

<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class SalaryComponent extends BaseModel
{
    protected $table = 'salary_components';

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
    public function salaryGroupComponents()
    {
        return $this->hasMany(SalaryGroupComponent::class, 'salary_component_id', 'id');
    }
    public function salaryComponent()
    {
        return $this->belongsTo(BasicSalaryDetails::class, 'id', 'salary_component_id');
    }
}

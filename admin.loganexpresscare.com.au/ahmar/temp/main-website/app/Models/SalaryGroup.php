<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class SalaryGroup extends BaseModel
{
    protected $table = 'salary_groups';

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
        return $this->hasMany(SalaryGroupComponent::class, 'salary_group_id', 'id');
    }

    public function salaryGroupUsers()
    {
        return $this->hasMany(SalaryGroupUser::class, 'salary_group_id', 'id');
    }
}

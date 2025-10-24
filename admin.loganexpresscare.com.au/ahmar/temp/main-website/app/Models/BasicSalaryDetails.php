<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class BasicSalaryDetails extends BaseModel
{
    protected $table = 'basic_salary_details';

    protected $default = ['xid', 'x_user_id', 'monthly', 'value_type'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id',  'user_id', 'salary_component_id'];

    protected $appends = ['xid', 'x_user_id', 'x_salary_component_id'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXSalaryComponentIdAttribute' => 'salary_component_id',
    ];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'salary_component_id' => Hash::class . ':hash',
    ];

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }

    public function salaryComponent()
    {
        return $this->hasMany(SalaryComponent::class, 'id', 'salary_component_id');
    }
}

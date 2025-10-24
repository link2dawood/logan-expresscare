<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Designation extends BaseModel
{
    protected $table = 'designations';

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid', 'employee_count'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCreatedByIdAttribute' => 'created_by',
    ];

    protected $casts = [
        'is_deletable' => 'integer',
        'created_by' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getEmployeeCountAttribute()
    {
        $employeeCount = StaffMember::where('designation_id', $this->id)
            ->count();

        return [
            'employee_count' => $employeeCount,
        ];
    }
}

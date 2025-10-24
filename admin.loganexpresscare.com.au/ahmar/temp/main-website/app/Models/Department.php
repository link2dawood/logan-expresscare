<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends BaseModel
{
    protected $table = 'departments';

    protected $default = ['xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'created_by'];

    protected $appends = ['xid', 'x_created_by', 'employee_count'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCreatedByAttribute' => 'created_by',
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
        $employeeCount = StaffMember::where('department_id', $this->id)
            ->count();

        return [
            'employee_count' => $employeeCount,
        ];
    }
}

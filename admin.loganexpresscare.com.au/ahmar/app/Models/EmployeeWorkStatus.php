<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class EmployeeWorkStatus extends BaseModel
{
    protected $table = 'employee_work_status';

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

    public function employeeWorkStatus()
    {
        return $this->belongsTo(EmployeeWorkStatus::class, 'employee_status_id', 'id');
    }
}

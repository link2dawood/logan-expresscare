<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\PrePayment;

class PayrollComponent extends BaseModel
{
    protected $table = 'payroll_components';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'pre_payment_id', 'payroll_id', 'user_id', 'salary_component_id', 'expense_id'];

    protected $appends = ['xid', 'x_pre_payment_id', 'x_payroll_id', 'x_user_id', 'x_salary_component_id', 'x_expense_id'];

    protected $hashableGetterFunctions = [
        'getXExpenseIdAttribute' => 'expense_id',
        'getXPayrollIdAttribute' => 'payroll_id',
        'getXPrePaymentIdAttribute' => 'pre_payment_id',
        'getXUserIdAttribute' => 'user_id',
        'getXSalaryComponentIdAttribute' => 'salary_component_id',
    ];

    protected $casts = [
        'expense_id' => Hash::class . ':hash',
        'salary_component_id' => Hash::class . ':hash',
        'payroll_id' => Hash::class . ':hash',
        'pre_payment_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'is_earning' => 'boolean',
        'amount' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function prePayment()
    {
        return $this->hasOne(PrePayment::class, 'id', 'pre_payment_id');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }
}

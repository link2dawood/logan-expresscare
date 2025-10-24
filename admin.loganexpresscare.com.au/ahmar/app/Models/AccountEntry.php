<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\PrePayment;

class AccountEntry extends BaseModel
{
    protected $table = 'account_entries';

    protected $default = ['xid', 'date'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'pre_payment_id', 'payroll_id', 'account_id', 'asset_id', 'deposit_id',  'expense_id'];

    protected $appends = ['xid', 'x_pre_payment_id', 'x_payroll_id', 'x_account_id', 'x_asset_id', 'x_deposit_id', 'x_expense_id'];

    protected $hashableGetterFunctions = [
        'getXPayrollIdAttribute' => 'payroll_id',
        'getXPrePaymentIdAttribute' => 'pre_payment_id',
        'getXAccountIdAttribute' => 'account_id',
        'getXAssetIdAttribute' => 'asset_id',
        'getXDepositIdAttribute' => 'deposit_id',
        'getXExpenseIdAttribute' => 'expense_id',
    ];

    protected $permissions = ['accounts_view'];

    protected $casts = [
        'payroll_id' => Hash::class . ':hash',
        'pre_payment_id' => Hash::class . ':hash',
        'account_id' => Hash::class . ':hash',
        'asset_id' => Hash::class . ':hash',
        'expense_id' => Hash::class . ':hash',
        'deposit_id' => Hash::class . ':hash',
        'is_debit' => 'boolean',
        'amount' => 'double',
        'date' => 'date'
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
        return $this->hasOne(Payroll::class, 'id', 'payroll_id');
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class, 'id', 'deposit_id');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class, 'id', 'expense_id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}

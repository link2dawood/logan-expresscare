<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Vinkla\Hashids\Facades\Hashids;

class Expense extends BaseModel
{
    protected $table = 'expenses';

    protected $default = ['xid'];

    protected $dates = ['date_time', 'reference_number', 'payment_date'];

    protected $guarded = ['id', 'warehouse_id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'warehouse_id', 'user_id', 'expense_category_id', 'account_id', 'payee_id'];

    protected $appends = ['xid', 'x_warehouse_id', 'x_user_id', 'x_expense_category_id', 'x_payee_id', 'x_account_id', 'bill_url'];

    protected $filterable = ['warehouse_id', 'expense_category_id', 'user_id', 'payee_id', 'account_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXWarehouseIdAttribute' => 'warehouse_id',
        'getXExpenseCategoryIdAttribute' => 'expense_category_id',
        'getXPayeeIdAttribute' => 'payee_id',
        'getXAccountIdAttribute' => 'account_id',
    ];

    protected $permissions = ['account_view'];

    protected $casts = [
        'date_time' => 'datetime',
        'warehouse_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'expense_category_id' => Hash::class . ':hash',
        'payee_id' => Hash::class . ':hash',
        'account_id' => Hash::class . ':hash',
        'amount' => 'double',
        'payment_date' => 'datetime',
        'payment_status' => 'integer',
        'payroll_month' => 'integer',
        'payroll_year' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getBillUrlAttribute()
    {
        $expenseBillPath = Common::getFolderPath('expenseBillPath');

        return $this->bill == null ? null : Common::getFileUrl($expenseBillPath, $this->bill);
    }

    public function expenseCategory()
    {
        return $this->hasOne(ExpenseCategory::class, 'id', 'expense_category_id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function payee()
    {
        return $this->hasOne(Payee::class, 'id', 'payee_id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}

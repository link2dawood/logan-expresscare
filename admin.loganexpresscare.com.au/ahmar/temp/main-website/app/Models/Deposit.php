<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Deposit extends BaseModel
{
    protected $table = 'deposits';

    protected $default = ['xid', 'amount', 'date_time', 'reference_number', 'file', 'notes'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'account_id', 'payer_id', 'deposit_category_id'];

    protected $appends = ['xid', 'x_company_id', 'x_account_id', 'x_payer_id', 'x_deposit_category_id', 'file_url'];

    protected $filterable = ['amount', 'account_id', 'payer_id', 'deposit_category_id'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXAccountIdAttribute' => 'account_id',
        'getXPayerIdAttribute' => 'payer_id',
        'getXDepositCategoryIdAttribute' => 'deposit_category_id',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'account_id' => Hash::class . ':hash',
        'payer_id' => Hash::class . ':hash',
        'deposit_category_id' => Hash::class . ':hash',
    ];

    public function getFileUrlAttribute()
    {
        $accountingFilePath = Common::getFolderPath('accountingFilePath');

        return $this->file == null ? null : Common::getFileUrl($accountingFilePath, $this->file);
    }


    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }

    public function payer()
    {
        return $this->hasOne(Payer::class, 'id', 'payer_id',);
    }

    public function depositCategory()
    {
        return $this->hasOne(DepositCategory::class, 'id', 'deposit_category_id');
    }
}

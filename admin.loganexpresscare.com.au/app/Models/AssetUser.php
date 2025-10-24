<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class AssetUser extends BaseModel
{
    protected $table = 'asset_users';

    protected $default = ['xid', 'lent_date', 'return_date', 'notes'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'asset_id', 'lent_to', 'lent_by', 'return_by', 'broken_user_id'];

    protected $appends = ['xid', 'x_asset_id', 'x_lent_to', 'x_lent_by', 'x_return_by', 'x_broken_user_id'];

    protected $filterable = [];

    protected $hashableGetterFunctions = [
        'getXAssetIdAttribute' => 'asset_id',
        'getXLentToAttribute' => 'lent_to',
        'getXLentByAttribute' => 'lent_by',
        'getXReturnByAttribute' => 'return_by',
        'getXBrokenUserIdAttribute' => 'broken_user_id',
    ];

    protected $casts = [
        'asset_id' => Hash::class . ':hash',
        'lent_to' => Hash::class . ':hash',
        'lent_by' => Hash::class . ':hash',
        'return_by' => Hash::class . ':hash',
        'broken_user_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'lent_to', 'id');
    }

    public function lentBy()
    {
        return $this->belongsTo(StaffMember::class, 'lent_by', 'id');
    }

    public function returnBy()
    {
        return $this->belongsTo(StaffMember::class, 'return_by', 'id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }
}

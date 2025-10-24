<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Classes\Common;

class Asset extends BaseModel
{
    protected $table = 'assets';

    protected $default = ['xid', 'name', 'image', 'serial_number', 'description'];

    protected $guarded = ['id', 'created_at', 'updated_at', 'old_account_id'];

    protected $hidden = ['id', 'location_id', 'user_id', 'asset_type_id', 'asset_user_id', 'account_id', 'broken_by'];

    protected $appends = ['xid', 'x_location_id', 'image_url', 'x_user_id', 'x_asset_type_id', 'x_asset_user_id', 'x_account_id', 'x_broken_by'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXLocationIdAttribute' => 'location_id',
        'getXUserIdAttribute' => 'user_id',
        'getXAssetTypeIdAttribute' => 'asset_type_id',
        'getXAssetUserIdAttribute' => 'asset_user_id',
        'getXAccountIdAttribute' => 'account_id',
        'getXBrokenByAttribute' => 'broken_by',
    ];

    protected $permissions = ['account_view', 'assets_view'];

    protected $casts = [
        'location_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'asset_type_id' => Hash::class . ':hash',
        'asset_user_id' => Hash::class . ':hash',
        'account_id' => Hash::class . ':hash',
        'broken_by' => Hash::class . ':hash',
        'purchase_date' => 'datetime',
        'price' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getImageUrlAttribute()
    {
        $assetImagePath = Common::getFolderPath('assetImagePath');

        return $this->image == null ? asset('images/asset.png') : Common::getFileUrl($assetImagePath, $this->image);
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function assetUser()
    {
        return $this->hasMany(AssetUser::class, 'asset_id', 'id');
    }

    public function brokenBy()
    {
        return $this->hasOne(StaffMember::class, 'id', 'broken_by');
    }

    public function return()
    {
        return $this->belongsTo(AssetUser::class, 'asset_user_id', 'id');
    }
}

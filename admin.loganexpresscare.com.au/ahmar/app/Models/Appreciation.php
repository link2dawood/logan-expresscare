<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Casts\Hash;
use App\Classes\Common;
use App\Models\StaffMember;
use App\Models\Award;
use App\Scopes\CompanyScope;

class Appreciation extends BaseModel
{
    protected $table = 'appreciations';

    protected $default = ['xid', 'date', 'description', 'price_amount', 'price_given', 'profile_image'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'award_id', 'user_id', 'created_by', 'letterhead_template_id', 'generates_id', 'account_id'];

    protected $appends = ['xid', 'x_company_id', 'x_award_id', 'x_user_id', 'x_created_by', 'profile_image_url', 'x_letterhead_template_id', 'x_generates_id', 'x_account_id'];

    protected $filterable = ['award_id'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXAwardIdAttribute' => 'award_id',
        'getXUserIdAttribute' => 'user_id',
        'getXCreatedByAttribute' => 'created_by',
        'getXLetterheadTemplateIdAttribute' => 'letterhead_template_id',
        'getXGeneratesIdAttribute' => 'generates_id',
        'getXAccountIdAttribute' => 'account_id',
    ];

    protected $permissions = ['appreciations_view', 'account_view'];

    protected $casts = [
        'date' => 'datetime',
        'user_id' => Hash::class . ':hash',
        'award_id' => Hash::class . ':hash',
        'letterhead_template_id' => Hash::class . ':hash',
        'generates_id' => Hash::class . ':hash',
        'account_id' => Hash::class . ':hash',
        'price_amount' => 'double',
        'price_given' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function award()
    {
        return $this->hasOne(Award::class, 'id', 'award_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function letterHeadTemplate()
    {
        return $this->belongsTo(LetterHeadTemplate::class, 'letterhead_template_id', 'id');
    }

    public function generate()
    {
        return $this->belongsTo(Generate::class, 'generates_id', 'id');
    }

    public function getProfileImageUrlAttribute()
    {
        $appreciationImagePath = Common::getFolderPath('appreciationImagePath');

        return $this->profile_image == null ? asset('images/appreciation.png') : Common::getFileUrl($appreciationImagePath, $this->profile_image);
    }
}

<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\Designation;

class IncrementPromotion extends BaseModel
{
    protected $table = 'increments_promotions';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'promoted_designation_id', 'current_designation_id', 'generate_id', 'letterhead_template_id'];

    protected $appends = ['xid', 'x_user_id', 'x_promoted_designation_id', 'x_current_designation_id', 'x_letterhead_template_id', 'x_generate_id'];

    protected $filterable = ['user_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXPromotedDesignationIdAttribute' => 'promoted_designation_id',
        'getXCurrentDesignationIdAttribute' => 'current_designation_id',
        'getXLetterheadTemplateIdAttribute' => 'letterhead_template_id',
        'getXGenerateIdAttribute' => 'generate_id',
    ];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'promoted_designation_id' => Hash::class . ':hash',
        'current_designation_id' => Hash::class . ':hash',
        'letterhead_template_id' => Hash::class . ':hash',
        'generate_id' => Hash::class . ':hash',
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function currentDesignation()
    {
        return $this->hasOne(Designation::class, 'id', 'current_designation_id');
    }

    public function promotedDesignation()
    {
        return $this->hasOne(Designation::class, 'id', 'promoted_designation_id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function letterHeadTemplate()
    {
        return $this->hasOne(LetterHeadTemplate::class, 'id', 'letterhead_template_id');
    }

    public function generate()
    {
        return $this->belongsTo(Generate::class, 'generate_id', 'id');
    }
}

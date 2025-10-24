<?php

namespace App\Models;

use App\Casts\Hash;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Generate extends BaseModel
{
    protected $table = 'generates';

    protected $default = ['xid', 'letterhead_template_id', 'x_letterhead_template_id', 'user_id', 'x_user_id', 'left_space', 'top_space', 'bottom_space', 'right_space', 'description', 'letterHeadTemplate', 'title'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'letterhead_template_id', 'user_id', 'admin_id'];

    protected $appends = ['xid', 'x_letterhead_template_id', 'x_user_id', 'x_admin_id'];

    protected $filterable = ['id', 'user_id', 'letterhead_template_id'];

    protected $hashableGetterFunctions = [
        'getXLetterheadTemplateIdAttribute' => 'letterhead_template_id',
        'getXUserIdAttribute' => 'user_id',
        'getXAdminIdAttribute' => 'admin_id',
    ];

    protected $casts = [
        'letterhead_template_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'admin_id' => Hash::class . ':hash',
        'left_space' => 'integer',
        'right_space' => 'integer',
        'top_space' => 'integer',
        'button_space' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function letterHeadTemplate()
    {
        return $this->belongsTo(LetterHeadTemplate::class, 'letterhead_template_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }
}

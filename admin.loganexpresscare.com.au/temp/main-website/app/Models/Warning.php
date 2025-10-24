<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Casts\Hash;

class Warning extends BaseModel
{
    protected $table = 'warnings';

    protected $default = ['xid', 'title', 'description', 'warning_date'];

    protected $guarded = ['id', 'manager_id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'user_id', 'letterhead_template_id', 'generates_id',];

    protected $appends = ['xid', 'x_user_id', 'x_letterhead_template_id', 'x_generates_id',];

    protected $filterable = ['title'];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'letterhead_template_id' => Hash::class . ':hash',
        'generates_id' => Hash::class . ':hash',
        'warning_date' => 'datetime',
    ];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXLetterheadTemplateIdAttribute' => 'letterhead_template_id',
        'getXGeneratesIdAttribute' => 'generates_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class, 'user_id', 'id');
    }

    public function letterHeadTemplate()
    {
        return $this->belongsTo(LetterHeadTemplate::class, 'letterhead_template_id', 'id');
    }

    public function generate()
    {
        return $this->belongsTo(Generate::class, 'generates_id', 'id');
    }
}

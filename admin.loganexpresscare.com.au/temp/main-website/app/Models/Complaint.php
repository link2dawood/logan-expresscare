<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Complaint extends BaseModel
{
    protected $table = 'complaints';

    protected $default = ['xid', 'title', 'status', 'description', 'proff_of_document', 'reply_notes'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'from_user_id', 'to_user_id', 'manager_id', 'letterhead_template_id', 'generates_id',];

    protected $appends = ['xid', 'x_from_user_id', 'x_to_user_id', 'proff_of_document_url', 'x_manager_id', 'x_letterhead_template_id', 'x_generates_id',];

    protected $filterable = ['title'];

    protected $hashableGetterFunctions = [
        'getXFromUserIdAttribute' => 'from_user_id',
        'getXToUserIdAttribute' => 'to_user_id',
        'getXManagerIdAttribute' => 'manager_id',
        'getXLetterheadTemplateIdAttribute' => 'letterhead_template_id',
        'getXGeneratesIdAttribute' => 'generates_id',

    ];

    protected $casts = [
        'from_user_id' => Hash::class . ':hash',
        'to_user_id' => Hash::class . ':hash',
        'manager_id' => Hash::class . ':hash',
        'letterhead_template_id' => Hash::class . ':hash',
        'generates_id' => Hash::class . ':hash',
        'date_time' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function fromStaff()
    {
        return $this->belongsTo(StaffMember::class, 'from_user_id', 'id');
    }

    public function toStaff()
    {
        return $this->belongsTo(StaffMember::class, 'to_user_id', 'id');
    }

    public function letterHeadTemplate()
    {
        return $this->belongsTo(LetterHeadTemplate::class, 'letterhead_template_id', 'id');
    }

    public function generate()
    {
        return $this->belongsTo(Generate::class, 'generates_id', 'id');
    }

    public function getProffOfDocumentUrlAttribute()
    {
        $proffOfDocumentPath = Common::getFolderPath('proffOfDocumentPath');

        return $this->proff_of_document == null ? asset('images/complaint.png') : Common::getFileUrl($proffOfDocumentPath, $this->proff_of_document);
    }
}

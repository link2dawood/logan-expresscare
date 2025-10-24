<?php

namespace App\Models\Self;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Models\StaffMember;

class SelfComplaint extends BaseModel
{
    protected $table = 'complaints';

    protected $default = ['xid', 'title', 'status', 'description', 'proff_of_document'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'from_user_id', 'to_user_id', 'manager_id'];

    protected $appends = ['xid', 'x_to_user_id', 'proff_of_document_url', 'x_manager_id'];

    protected $filterable = ['title'];

    protected $hashableGetterFunctions = [
        'getXToUserIdAttribute' => 'to_user_id',
        'getXManagerIdAttribute' => 'manager_id',

    ];

    protected $casts = [
        'to_user_id' => Hash::class . ':hash',
        'manager_id' => Hash::class . ':hash',
        'date_time' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function toStaff()
    {
        return $this->belongsTo(StaffMember::class, 'to_user_id', 'id');
    }

    public function getProffOfDocumentUrlAttribute()
    {
        $proffOfDocumentPath = Common::getFolderPath('proffOfDocumentPath');

        return $this->proff_of_document == null ? asset('images/complaint.png') : Common::getFileUrl($proffOfDocumentPath, $this->proff_of_document);
    }
}

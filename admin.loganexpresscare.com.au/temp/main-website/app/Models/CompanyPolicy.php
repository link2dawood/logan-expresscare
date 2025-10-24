<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class CompanyPolicy extends BaseModel
{
    protected $table = 'company_policies';

    protected $default = ['xid', 'title','policy_document', 'description'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'location_id'];

    protected $appends = ['xid', 'x_company_id', 'x_location_id', 'policy_document_url'];

    protected $filterable = ['title'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXLocationIdAttribute' => 'location_id',
    ];

    protected $casts = [
        'location_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getPolicyDocumentUrlAttribute()
    {
        $policyDocumentPath = Common::getFolderPath('policyDocumentPath');

        return $this->policy_document == null ? null : Common::getFileUrl($policyDocumentPath, $this->policy_document);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }
}

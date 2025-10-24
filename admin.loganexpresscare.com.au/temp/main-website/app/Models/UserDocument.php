<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use App\Classes\Common;

class UserDocument extends BaseModel
{
    protected $table = 'user_documents';

    protected $default = ['xid', 'values'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id', 'user_id', 'field_type_id'];

    protected $appends = ['xid', 'x_company_id', 'x_user_id', 'x_field_type_id', 'values_url'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXUserIdAttribute' => 'user_id',
        'getXFieldTypeIdAttribute' => 'field_type_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function fieldTypes()
    {
        return $this->belongsTo(EmployeeFields::class, 'field_type_id', 'id');
    }

    public function getValuesUrlAttribute()
    {
        $userDocumentPath = Common::getFolderPath('userDocumentPath');
        if ($this->fieldTypes['type'] == 'file') {
            return $this->values == null ? null : Common::getFileUrl($userDocumentPath, $this->values);
        } else {
            return;
        }
    }
}

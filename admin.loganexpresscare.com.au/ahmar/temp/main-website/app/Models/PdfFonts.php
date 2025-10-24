<?php

namespace App\Models;

use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Illuminate\Support\Str;

class PdfFonts extends BaseModel
{
    protected $table = 'pdf_fonts';

    protected $default = ['xid', 'name', 'file', 'user_kashida', 'use_otl'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'company_id'];

    protected $appends = ['xid', 'short_name', 'x_company_id', 'file_url'];

    protected $filterable = ['name'];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getShortNameAttribute()
    {
        return Str::snake($this->name);
    }

    public function getFileUrlAttribute()
    {
        $pdfFontFilePath = Common::getFolderPath('pdfFontFilePath');

        return $this->file == null ? null : Common::getFileUrl($pdfFontFilePath, $this->file);
    }
}

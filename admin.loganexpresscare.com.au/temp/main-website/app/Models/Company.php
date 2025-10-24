<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;

class Company extends BaseModel
{
    use Billable, Notifiable;

    protected $table = 'companies';

    protected $dates = ['licence_expire_on'];

    protected $default = ['xid'];

    protected $guarded = ['id', 'is_global', 'subscription_plan_id', 'licence_expire_on', 'package_type', 'stripe_id', 'trial_ends_at',  'created_at', 'updated_at'];

    protected $hidden = ['id', 'currency_id', 'warehouse_id', 'admin_id', 'lang_id', 'subscription_plan_id', 'updated_at', 'pdf_font_id', 'google_geo_coding_api_key'];

    protected $appends = ['xid', 'x_currency_id', 'x_warehouse_id', 'x_admin_id', 'x_lang_id', 'x_subscription_plan_id', 'login_image_url', 'light_logo_url', 'dark_logo_url', 'dark_logo_public_url', 'small_light_logo_url', 'small_dark_logo_url', 'beep_audio_url', 'profile_image_url', 'x_pdf_font_id', 'masked_google_geo_coding_api_key'];

    protected $hashableGetterFunctions = [
        'getXCurrencyIdAttribute' => 'currency_id',
        'getXWarehouseIdAttribute' => 'warehouse_id',
        'getXAdminIdAttribute' => 'admin_id',
        'getXLangIdAttribute' => 'lang_id',
        'getXSubscriptionPlanIdAttribute' => 'subscription_plan_id',
        'getXPdfFontIdAttribute' => 'pdf_font_id',
    ];

    protected $casts = [
        'licence_expire_on' => 'datetime',
        'warehouse_id' => Hash::class . ':hash',
        'currency_id' => Hash::class . ':hash',
        'admin_id' => Hash::class . ':hash',
        'lang_id' => Hash::class . ':hash',
        'subscription_plan_id' => Hash::class . ':hash',
        'app_debug' => 'integer',
        'rtl' => 'integer',
        'auto_detect_timezone' => 'integer',
        'update_app_notification' => 'integer',
        'is_global' => 'integer',
        'verified' => 'integer',
        'allowed_ip_address' => 'json',
        'late_mark_after' => 'integer',
        'employee_id_digits' => 'integer',
        'self_clocking' => 'integer',
        'early_clock_in_time' => 'integer',
        'allow_clock_out_till' => 'integer',
        'letterhead_show_company_name' => 'integer',
        'letterhead_show_company_email' => 'integer',
        'letterhead_show_company_phone' => 'integer',
        'letterhead_show_company_address' => 'integer',
        'letterhead_title_show_in_pdf' => 'integer',
        'letterhead_left_space' => 'integer',
        'letterhead_right_space' => 'integer',
        'letterhead_top_space' => 'integer',
        'letterhead_button_space' => 'integer',
        'use_custom_font' => 'integer',
        'capture_location' => 'integer',
    ];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('companies.is_global', 0);
        });
    }

    public function getLightLogoUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->light_logo == null ? asset('images/light.png') : Common::getFileUrl($companyLogoPath, $this->light_logo);
    }

    public function getDarkLogoUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->dark_logo == null ? asset('images/dark.png') : Common::getFileUrl($companyLogoPath, $this->dark_logo);
    }

    public function getDarkLogoPublicUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->dark_logo == null ? public_path('images/dark.png') : Common::getFileUrl($companyLogoPath, $this->dark_logo, 'public');
    }

    public function getSmallDarkLogoUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->small_dark_logo == null ? asset('images/small_dark.png') : Common::getFileUrl($companyLogoPath, $this->small_dark_logo);
    }

    public function getSmallLightLogoUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->small_light_logo == null ? asset('images/small_light.png') : Common::getFileUrl($companyLogoPath, $this->small_light_logo);
    }

    public function getLoginImageUrlAttribute()
    {
        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        return $this->login_image == null ? asset('images/login_background.svg') : Common::getFileUrl($companyLogoPath, $this->login_image);
    }

    public function getProfileImageUrlAttribute()
    {
        $frontJobDetailImagePath = Common::getFolderPath('frontJobDetailImagePath');

        return $this->profile_image == null ? asset('jobDetail/jobDetails.png') : Common::getFileUrl($frontJobDetailImagePath, $this->profile_image);
    }

    // Accessor for masked API key
    public function getMaskedGoogleGeoCodingApiKeyAttribute()
    {
        $key = $this->attributes['google_geo_coding_api_key'] ?? '';

        if (strlen($key) <= 8) {
            return str_repeat('*', strlen($key));
        }

        $start = substr($key, 0, 4);
        $end   = substr($key, -4);

        return $start . str_repeat('*', strlen($key) - 8) . $end;
    }

    public function getBeepAudioUrlAttribute()
    {
        return asset('images/beep.wav');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function admin()
    {
        return $this->belongsTo(StaffMember::class, 'admin_id', 'id');
    }

    public function pdfFont()
    {
        return $this->belongsTo(PdfFonts::class, 'pdf_font_id', 'id');
    }
}

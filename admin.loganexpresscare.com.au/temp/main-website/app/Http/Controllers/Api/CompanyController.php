<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Company\UpdateRequest;
use App\Http\Requests\Api\Company\UpdatePdfFontSettingRequest;
use App\Models\Company;
use App\Models\Settings;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Http\Request;

class CompanyController extends ApiBaseController
{
    protected $model = Company::class;

    protected $updateRequest = UpdateRequest::class;
    protected $updatePdfFontSettingRequest = UpdatePdfFontSettingRequest::class;

    public function updating(Company $company)
    {
        if (env('APP_ENV') == 'production' && ($company->isDirty('name') ||
            $company->isDirty('short_name') || $company->isDirty('light_logo') ||
            $company->isDirty('dark_logo') || $company->isDirty('small_dark_logo') ||
            $company->isDirty('small_light_logo') || $company->isDirty('app_debug') ||
            $company->isDirty('update_app_notification') || $company->isDirty('app_debug')
        )) {
            throw new ApiException('Not Allowed In Demo Mode');
        }

        if (
            str_contains($company->google_geo_coding_api_key, '****')
        ) {
            $company->google_geo_coding_api_key = $company->getOriginal('google_geo_coding_api_key');
        }

        return $company;
    }

    public function updateCreateMenu(Request $request)
    {
        $company = company();
        $company->shortcut_menus = $request->position;
        $company->save();

        // Setting for create menu
        $settingCreateMenu = Settings::where('setting_type', 'shortcut_menus')->first();

        if ($settingCreateMenu) {
            $settingCreateMenu->credentials = $request->shortcut_menus_settings;
            $settingCreateMenu->save();
        }

        return ApiResponse::make('Success', []);
    }

    public function updatePdfFontSetting(UpdatePdfFontSettingRequest $updatePdfFontSettingRequest)
    {
        $company = company();

        // here we have to check permission
        $loggedUser = user();

        if (!$loggedUser->ability('admin', 'font_settings')) {
            throw new ApiException("Not have valid permission");
        }

        if ($company) {
            $company->use_custom_font = $updatePdfFontSettingRequest->use_custom_font;

            if ($updatePdfFontSettingRequest->use_custom_font == 0) {
                $company->pdf_font_id = null;
            } else {
                $company->pdf_font_id = $this->getIdFromHash($updatePdfFontSettingRequest->pdf_font_id);
            }

            $company->letterhead_header_color = $updatePdfFontSettingRequest->letterhead_header_color;
            $company->letterhead_show_company_name = $updatePdfFontSettingRequest->letterhead_show_company_name;
            $company->letterhead_show_company_email = $updatePdfFontSettingRequest->letterhead_show_company_email;
            $company->letterhead_show_company_phone = $updatePdfFontSettingRequest->letterhead_show_company_phone;
            $company->letterhead_show_company_address = $updatePdfFontSettingRequest->letterhead_show_company_address;
            $company->letterhead_left_space = $updatePdfFontSettingRequest->letterhead_left_space;
            $company->letterhead_right_space = $updatePdfFontSettingRequest->letterhead_right_space;
            $company->letterhead_top_space = $updatePdfFontSettingRequest->letterhead_top_space;
            $company->letterhead_bottom_space = $updatePdfFontSettingRequest->letterhead_bottom_space;
            $company->holiday_pdf_font_size = $updatePdfFontSettingRequest->holiday_pdf_font_size;
            $company->holiday_pdf_line_height = $updatePdfFontSettingRequest->holiday_pdf_line_height;
            $company->generate_pdf_font_size = $updatePdfFontSettingRequest->generate_pdf_font_size;
            $company->generate_pdf_line_height = $updatePdfFontSettingRequest->generate_pdf_line_height;
            $company->letterhead_title_show_in_pdf = $updatePdfFontSettingRequest->letterhead_title_show_in_pdf;
            $company->export_pdf_font_size = $updatePdfFontSettingRequest->export_pdf_font_size;
            $company->export_pdf_line_height = $updatePdfFontSettingRequest->export_pdf_line_height;
            $company->export_pdf_left_space = $updatePdfFontSettingRequest->export_pdf_left_space;
            $company->export_pdf_right_space = $updatePdfFontSettingRequest->export_pdf_right_space;
            $company->export_pdf_top_space = $updatePdfFontSettingRequest->export_pdf_top_space;
            $company->export_pdf_bottom_space = $updatePdfFontSettingRequest->export_pdf_bottom_space;
            $company->save();
        }

        return ApiResponse::make('Success', []);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EmailTemplate\IndexRequest;
use App\Http\Requests\Api\EmailTemplate\UpdateRequest;
use App\Models\Settings;
use Examyou\RestAPI\Exceptions\ApiException;

class EmailTemplateController extends ApiBaseController
{
    protected $model = Settings::class;

    protected $indexRequest = IndexRequest::class;
    protected $updateRequest = UpdateRequest::class;

    protected function modifyIndex($query)
    {
        $company = company();
        $loggedUser = user();
        if (!$loggedUser->ability('admin', 'email_edit')) {
            throw new ApiException("Not have valid permission");
        }

        $query = $query->where('setting_type', 'email_templates')
            ->where('company_id', $company->id);

        $type = request('type');
        if ($type === 'company') {
            $query = $query->where('name_key', 'like', 'company_%');
        } elseif ($type === 'employee') {
            $query = $query->where('name_key', 'like', 'employee_%');
        }

        return  $query;
    }

    public function updating(Settings $settings)
    {
        $loggedUser = user();
        if (!$loggedUser->ability('admin', 'email_edit')) {
            throw new ApiException("Not have valid permission");
        }

        $request = request();
        $settings->name = $request->has('credentials') && $request->credentials['title'] ? $request->credentials['title'] : $settings->name;

        return  $settings;
    }
}

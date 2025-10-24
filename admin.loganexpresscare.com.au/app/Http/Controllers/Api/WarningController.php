<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Warning\IndexRequest;
use App\Http\Requests\Api\Warning\StoreRequest;
use App\Http\Requests\Api\Warning\UpdateRequest;
use App\Http\Requests\Api\Warning\DeleteRequest;
use App\Models\Generate;
use App\Models\Warning;

class WarningController extends ApiBaseController
{
    protected $model = Warning::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('warning_date') && $request->warning_date != '') {
            $warningDate = explode(',', $request->warning_date);
            $startDate = $warningDate[0];
            $endDate = $warningDate[1];

            $query = $query->whereRaw('warnings.warning_date >= ?', [$startDate])
                ->whereRaw('warnings.warning_date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'warnings_view')) {
            $query = $this->applyVisibility($query, 'warnings');

            if ($request->has('user_id')) {
                $query = $query->where('warnings.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('warnings.user_id', $loggedUser->id);
        }

        return $query;
    }
    public function storing($warning)
    {
        $request = request();
        $loggedUser = user();

        $warning->manager_id = $loggedUser->id;

        if ($warning->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->user_id = $warning->user_id;
            $generate->letterhead_template_id = $warning->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $warning->generates_id = $generate->id;
        }

        return $warning;
    }

    public function stored($warning)
    {
        // Notifying to User
        Notify::send('employee_warning', $warning);

        return $warning;
    }

    public function updating($warning)
    {

        //create and update letter head generate
        CommonHrm::createUpdateGenerate($warning);

        return $warning;
    }
}

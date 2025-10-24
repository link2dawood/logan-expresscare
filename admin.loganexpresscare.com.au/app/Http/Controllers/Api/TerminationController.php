<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Termination\IndexRequest;
use App\Http\Requests\Api\Termination\StoreRequest;
use App\Http\Requests\Api\Termination\UpdateRequest;
use App\Http\Requests\Api\Termination\DeleteRequest;
use App\Models\Generate;
use App\Models\Offboarding;

class TerminationController extends ApiBaseController
{
    protected $model = Offboarding::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        $query = $query->where('offboardings.type', 'termination');

        if ($request->has('start_date') && $request->start_date != '') {
            $terminationDate = explode(',', $request->start_date);
            $startDate = $terminationDate[0];
            $endDate = $terminationDate[1];

            $query = $query->whereRaw('offboardings.start_date >= ?', [$startDate])
                ->whereRaw('offboardings.start_date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'terminations_view')) {
            $query = $this->applyVisibility($query, 'offboardings');

            if ($request->has('user_id')) {
                $query = $query->where('offboardings.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('offboardings.user_id', $loggedUser->id);
        }

        return  $query;
    }

    public function storing($offboarding)
    {
        $request = request();
        $loggedUser = user();
        $offboarding->type = 'termination';
        $offboarding->status = "approved";
        if ($offboarding->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->user_id = $offboarding->user_id;
            $generate->letterhead_template_id = $offboarding->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $offboarding->generates_id = $generate->id;
        }
        return $offboarding;
    }

    public function stored($offboarding)
    {
        if ($offboarding->status == 'approved') {
            // Notifying to User
            Notify::send('employee_terminations', $offboarding);
        }

        return $offboarding;
    }

    public function updating($offboarding)
    {
        //create and update letter head generate
        CommonHrm::createUpdateGenerate($offboarding);

        return $offboarding;
    }
}

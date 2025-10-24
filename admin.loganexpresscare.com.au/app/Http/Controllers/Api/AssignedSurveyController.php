<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\AssignedSurvey\IndexRequest;
use App\Http\Requests\Api\AssignedSurvey\StoreRequest;
use App\Http\Requests\Api\AssignedSurvey\UpdateRequest;
use App\Http\Requests\Api\AssignedSurvey\DeleteRequest;
use App\Models\Feedback;
use App\Models\FeedbackUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssignedSurveyController extends ApiBaseController
{
    protected $model = Feedback::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $query = $query->where('feedbacks.visible_to', 1);

        return $query;
    }
}

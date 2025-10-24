<?php

namespace App\Http\Controllers\Api\Self;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\FeedbackUser\IndexRequest;
use App\Http\Requests\Api\Self\FeedbackUser\StoreRequest;
use App\Http\Requests\Api\Self\FeedbackUser\UpdateRequest;
use App\Http\Requests\Api\Self\FeedbackUser\DeleteRequest;
use App\Http\Requests\Api\Self\AssignedSurvey\AssignStoreRequest;
use App\Models\FeedbackUser;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;

class FeedbackUserController extends ApiBaseController
{
    protected $model = FeedbackUser::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $assignStoreRequest = AssignStoreRequest::class;


    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();
        $today = Carbon::now();

        if ($request->has('title') && $request->title != '') {
            $title = $request->title;
            $query = $query->join('feedbacks', 'feedback_users.feedback_id', '=', 'feedbacks.id')
                ->where('feedbacks.title', 'LIKE', '%' . $title . '%');
        }

        $query = $query->where('user_id', $user->id);

        if ($request->has('year')) {
            $year = $request->year;
            $query = $query->join('feedbacks', 'feedback_users.feedback_id', '=', 'feedbacks.id')
                ->whereYear('last_date', $year);
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('last_date', $request->month);
        }

        if ($request->has('status') && $request->status != '' && $request->status == 'active') {
            $query = $query->whereDate('feedbacks.last_date', '>', $today->format('Y-m-d h:i:s'));
        } elseif ($request->status == 'expired') {
            $query = $query->whereDate('feedbacks.last_date', '<', $today->format('Y-m-d h:i:s'));
        }

        return  $query;
    }

    public function toSetEmployeeFeedback(AssignStoreRequest $request)
    {
        $loggedUser = user();

        $feedbackId = $this->getIdFromHash($request->x_feedback_id);
        $feedbackUserId = $this->getIdFromHash($request->xid);

        $employeeSurvey = FeedbackUser::find($feedbackUserId);
        if ($employeeSurvey->feedback_given == '1') {
            throw new ApiException("You have already submited this form!");
        } else {
            $employeeSurvey->feedback_id = $feedbackId;
            $employeeSurvey->user_id = $loggedUser->id;
            $employeeSurvey->feedback_given = 1;
            $employeeSurvey->data =  $request->feedback['feedback_form_fields'];
            $employeeSurvey->submit_date = Carbon::now()->format('Y-m-d h:i:s');
            $employeeSurvey->save();

            // Notifying to company
            Notify::send('company_employee_survey_submit', $employeeSurvey);

            return ApiResponse::make('Success');
        }
    }
}

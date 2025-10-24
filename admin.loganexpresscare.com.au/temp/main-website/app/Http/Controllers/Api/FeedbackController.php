<?php

namespace App\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Feedback\IndexRequest;
use App\Http\Requests\Api\Feedback\StoreRequest;
use App\Http\Requests\Api\Feedback\UpdateRequest;
use App\Http\Requests\Api\Feedback\DeleteRequest;
use App\Http\Requests\Api\Self\FeedbackUser\RatingStoreRequest;
use App\Models\Feedback;
use App\Models\FeedbackUser;
use App\Models\StaffMember;
use Examyou\RestAPI\ApiResponse;
use Illuminate\Support\Carbon;

class FeedbackController extends ApiBaseController
{
    protected $model = Feedback::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
    protected $ratingStoreRequest = RatingStoreRequest::class;

    public $isCreate = false;

    public function modifyIndex($query)
    {
        $request = request();
        $today = Carbon::now();

        if ($request->has('status') && $request->status != '' && $request->status == 'active') {
            $query = $query->whereDate('feedbacks.last_date', '>', $today->format('Y-m-d h:i:s'));
        } elseif ($request->status == 'expired') {
            $query = $query->whereDate('feedbacks.last_date', '<', $today->format('Y-m-d h:i:s'));
        }

        if ($request->has('month')) {
            $query = $query->whereMonth('last_date', $request->month);
        }

        if ($request->has('year')) {
            $query = $query->whereYear('last_date', $request->year);
        }

        return $query;
    }

    public function stored(Feedback $feedback)
    {
        $this->isCreate = true;

        $this->addAndUpdateFeedback($feedback);
    }

    public function updated(Feedback $feedback)
    {
        FeedbackUser::where('feedback_id', $feedback->id)
            ->where('feedback_given', 0)
            ->delete();

        $this->addAndUpdateFeedback($feedback);
    }

    public function addAndUpdateFeedback(Feedback $feedback)
    {
        $request = request();
        $allUsers = $feedback->visible_to == 1 ? StaffMember::where('status', 'active')->pluck('id')->toArray() : $this->getIdArrayFromHash($request->user_id);
        foreach ($allUsers as $userId) {
            $feedbackUser = FeedbackUser::where('feedback_id', $feedback->id)->where('user_id', $userId)->first();
            if (!$feedbackUser) {
                $feedbackUser = new FeedbackUser();
            }
            $feedbackUser->feedback_id = $feedback->id;
            $feedbackUser->user_id = $userId;
            $feedbackUser->save();

            // Sending mail only first time create
            if ($this->isCreate) {
                // Notifying to User
                Notify::send('employee_survey_forms_assign', $feedbackUser);
            }
        }

        return $feedback;
    }

    public function createFeedbackRating(RatingStoreRequest $request)
    {
        $feedbackUserId = $this->getIdFromHash($request->xid);
        $feedbackUser = FeedbackUser::find($feedbackUserId);
        $ratingDetails = $request->rating_details;
        $feedbackUser->rating_details = $ratingDetails;
        $givenRateing = 0;
        $totalAvrageRateing = 0;
        $count = 0;

        foreach ($ratingDetails as $detail) {
            foreach ($detail['fields'] as $field) {
                if (!isset($field['rating_details'])) {
                    $field['rating_details'] = 0;
                }
                $givenRateing += $field['rating_details'];
                $count++;
            }
        }
        $totalAvrageRateing = $count;
        $feedbackUser->rating = $givenRateing / $totalAvrageRateing;

        $feedbackUser->save();
        return ApiResponse::make('Rating Updated successfully', [
            'rating' => 'success'
        ]);
    }
}

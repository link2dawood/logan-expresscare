<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\FeedbackUser\IndexRequest;
use App\Http\Requests\Api\Self\FeedbackUser\StoreRequest;
use App\Http\Requests\Api\Self\FeedbackUser\UpdateRequest;
use App\Http\Requests\Api\Self\FeedbackUser\DeleteRequest;
use App\Models\FeedbackUser;

class FeedbackUserController extends ApiBaseController
{
    protected $model = FeedbackUser::class;
    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('feedback_id') && $request->feedback_id != null && $request->has('type')) {
            $feedbackId = $this->getIdFromHash($request->feedback_id);

            if ($request->has('type') && $request->type == "replied") {
                $query = $query->where('feedback_given', 1)->where('feedback_id', $feedbackId);
            } else {
                $query = $query->where('feedback_given', 0)->where('feedback_id', $feedbackId);
            }
        }

        if ($loggedUser->ability('admin', 'feedbacks_view')) {
            $query = $this->applyVisibility($query, 'feedback_users');

            if ($request->has('user_id')) {
                $query = $query->where('feedback_users.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('feedback_users.user_id', $loggedUser->id);
        }

        return  $query;
    }
}

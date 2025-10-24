<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Award\IndexRequest;
use App\Http\Requests\Api\Award\StoreRequest;
use App\Http\Requests\Api\Award\UpdateRequest;
use App\Http\Requests\Api\Award\DeleteRequest;
use App\Models\Award;
use App\Models\Appreciation;
use Examyou\RestAPI\Exceptions\ApiException;

class AwardController extends ApiBaseController
{
    protected $model = Award::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function destroying(Award $award)
    {
        // Can not delete award if it is assigned to some appreciation
        $assignAppreciationNotDelete = Appreciation::where('award_id', $award->id)->count();

        if ($assignAppreciationNotDelete > 0) {
            throw new ApiException('Award assigned to some appreciation is not deletable.');
        }

        return $award;
    }

    public function storing($award)
    {
        $loggedUser = user();

        $award->created_by = $loggedUser->id;

        return $award;
    }
}

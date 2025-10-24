<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Indicator\IndexRequest;
use App\Http\Requests\Api\Indicator\StoreRequest;
use App\Http\Requests\Api\Indicator\UpdateRequest;
use App\Http\Requests\Api\Indicator\DeleteRequest;
use App\Models\Indicator;
use Examyou\RestAPI\ApiResponse;

class IndicatorController extends ApiBaseController
{
    protected $model = Indicator::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;
}

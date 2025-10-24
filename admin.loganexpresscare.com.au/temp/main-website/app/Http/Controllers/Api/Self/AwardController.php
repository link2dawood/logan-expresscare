<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Award\IndexRequest;
use App\Models\Award;

class AwardController extends ApiBaseController
{
    protected $model = Award::class;

    protected $indexRequest = IndexRequest::class;

}
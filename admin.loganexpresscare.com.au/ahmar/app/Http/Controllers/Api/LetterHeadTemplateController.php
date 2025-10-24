<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\LetterheadTemplate\IndexRequest;
use App\Http\Requests\Api\LetterheadTemplate\StoreRequest;
use App\Http\Requests\Api\LetterheadTemplate\UpdateRequest;
use App\Http\Requests\Api\LetterheadTemplate\DeleteRequest;
use App\Models\LetterHeadTemplate;

class LetterHeadTemplateController extends ApiBaseController
{
	protected $model = LetterHeadTemplate::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;
}

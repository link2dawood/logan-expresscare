<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Generate\IndexRequest;
use App\Http\Requests\Api\Generate\StoreRequest;
use App\Http\Requests\Api\Generate\UpdateRequest;
use App\Http\Requests\Api\Generate\DeleteRequest;
use App\Models\Generate;

class GenerateController extends ApiBaseController
{
	protected $model = Generate::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	protected function modifyIndex($query)
	{
		$request = request();

		if ($request->has('user_id') && ($request->user_id != "" || $request->user_id != null)) {
			$userId = $this->getIdFromHash($request->user_id);
			$query = $query->where('generates.user_id', $userId);
		};

		if ($request->has('letterhead_template_id') && ($request->letterhead_template_id != "" || $request->letterhead_template_id != null)) {
			$letterId = $this->getIdFromHash($request->letterhead_template_id);
			$query = $query->where('generates.letterhead_template_id', $letterId);
		};

		return  $query;
	}
}

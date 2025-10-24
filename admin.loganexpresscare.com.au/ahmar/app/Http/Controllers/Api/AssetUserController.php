<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\AssetUser\IndexRequest;

use App\Models\AssetUser;

class AssetUserController extends ApiBaseController
{
	protected $model = AssetUser::class;

	protected $indexRequest = IndexRequest::class;

	protected function modifyIndex($query)
	{
		$request = request();


		if ($request->has('user_id') && ($request->user_id != "" || $request->user_id != null)) {
			$userId = $this->getIdFromHash($request->user_id);
			$query = $query->where('asset_users.lent_to', $userId);
		};

		if ($request->has('asset_id') && ($request->asset_id != "" || $request->asset_id != null)) {
			$assetId = $this->getIdFromHash($request->asset_id);
			$query = $query->where('asset_users.asset_id', $assetId);
		};

		return  $query;
	}
}

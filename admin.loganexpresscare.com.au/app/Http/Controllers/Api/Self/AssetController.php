<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Self\Asset\IndexRequest;
use App\Models\AssetUser;

class AssetController extends ApiBaseController
{
    protected $model = AssetUser::class;

    protected $indexRequest = IndexRequest::class;

    protected function modifyIndex($query)
    {
        $user = user();
        $request = request();

        $query = $query->where('lent_to', $user->id);

        if ($request->has('asset_type_id') && ($request->asset_type_id != "" || $request->asset_type_id != null)) {
            $assetTypeId = $this->getIdFromHash($request->asset_type_id);
            $query = $query->join('assets', 'assets.id', '=', 'asset_users.id')->where('assets.asset_type_id', $assetTypeId);
        };

        if ($request->has('status') && ($request->status == "broken" && $request->status != null)) {
            $query = $query->where('broken_user_id', $user->id);
        };


        return  $query;
    }
}

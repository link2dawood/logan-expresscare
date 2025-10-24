<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Asset\IndexRequest;
use App\Http\Requests\Api\Asset\StoreRequest;
use App\Http\Requests\Api\Asset\UpdateRequest;
use App\Http\Requests\Api\Asset\DeleteRequest;
use App\Http\Requests\Api\Asset\UpdateAssetRequest;
use App\Models\AccountEntry;
use App\Models\Asset;
use App\Models\AssetUser;
use Examyou\RestAPI\ApiResponse;

class AssetController extends ApiBaseController
{
    protected $model = Asset::class;

    public $oldAccountId = null;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    protected function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();


        if ($request->has('user_id') && ($request->user_id != "" || $request->user_id != null)) {
            $userId = $this->getIdFromHash($request->user_id);
            $query = $query->where('assets.user_id', $userId);
        };

        if ($request->has('location_id') && ($request->location_id != "" || $request->location_id != null)) {
            $locationId = $this->getIdFromHash($request->location_id);
            $query = $query->where('assets.location_id', $locationId);
        };

        if ($request->has('status') && $request->status != "") {
            $attendance = $request->status;
            $query = $query->where('assets.status', $attendance);
        };

        return  $query;
    }

    public function storing(Asset $asset)
    {
        $request = request();

        if ($request->has('price') && ($request->price == "" || $request->price == null)) {
            $asset->price = 0;
        } else {
            $asset->price = $request->price;
        }
        return $asset;
    }

    public function stored(Asset $asset)
    {
        $request = request();
        if ($request->has('account_id') && $request->account_id != "" || $request->account_id != null) {
            CommonHrm::insertAccountEntries($asset->account_id, null, "asset", $asset->purchase_date, $asset->id, $asset->price);
            CommonHrm::updateAccountAmount($asset->account_id);
        }
    }

    public function updating(Asset $asset)
    {
        $request = request();

        if ($request->has('price') && ($request->price == "" || $request->price == null)) {
            $asset->price = 0;
        } else {
            $asset->price = $request->price;
        }

        $this->oldAccountId = $asset->getOriginal('account_id');

        return $asset;
    }

    public function updated(Asset $asset)
    {
        $request = request();

        if ($request->has('account_id') && $request->account_id != "" || $request->account_id != null) {
            CommonHrm::insertAccountEntries($asset->account_id, $this->oldAccountId, "asset", $asset->purchase_date, $asset->id, $asset->price);
            CommonHrm::updateAccountAmount($asset->account_id);

            if ($this->oldAccountId && $this->oldAccountId != null && $this->oldAccountId != $asset->account_id) {
                CommonHrm::updateAccountAmount($this->oldAccountId);
            }
        } elseif ($this->oldAccountId && $asset->account_id == null) {
            AccountEntry::where('account_id', $this->oldAccountId)->where('type', "asset")->where('asset_id', $asset->id)->delete();
            $asset->price = 0;
            $asset->save();
            CommonHrm::updateAccountAmount($this->oldAccountId);
        }
    }

    public function destroyed(Asset $asset)
    {
        CommonHrm::updateAccountAmount($asset->account_id);
    }

    public function addAssetToLend(UpdateAssetRequest $request)
    {
        $loggedUser = user();
        $id = $this->getIdFromHash($request->xid);
        $lentTo = $this->getIdFromHash($request->user_id);

        $assetUser = new AssetUser();
        $assetUser->asset_id = $id;
        $assetUser->lent_to = $lentTo;
        $assetUser->lend_date = $request->lend_date;
        $assetUser->return_date = $request->return_date;
        $assetUser->lent_by = $loggedUser->id;
        $assetUser->notes = $request->note;
        $assetUser->save();

        $asset = Asset::find($id);
        $asset->user_id = $lentTo;
        $asset->asset_user_id = $assetUser->id;
        $asset->save();

        // Notifying to User
        Notify::send('employee_asset_lent', $assetUser);

        return ApiResponse::make('Add User Successfully', []);
    }

    public function removeLendAsset()
    {
        $request = request();
        $loggedUser = user();
        $AssetUserid = $this->getIdFromHash($request->xid);
        if ($request->edit != null && $request->actual_return_date != null) {
            $Assetid = $this->getIdFromHash($request->asset_id);

            $asset = Asset::find($Assetid);
            if ($asset) {
                $brokenByUser = $asset->user_id;
                $asset->user_id = null;
                $asset->asset_user_id = null;

                $assetUser = AssetUser::find($AssetUserid);
                if ($request->has('is_broken') && $request->is_broken == '1') {
                    $asset->status = 'not_working';
                    $asset->broken_by = $brokenByUser;
                    $assetUser->broken_user_id = $brokenByUser;
                }
                $assetUser->actual_return_date = $request->actual_return_date;
                $assetUser->return_by = $loggedUser->id;
                $assetUser->notes = $request->notes;
                $assetUser->save();
                $asset->save();
            }
        } else {

            $assetUser = AssetUser::find($AssetUserid);
            $assetUser->return_date = $request->return_date;
            $assetUser->notes = $request->notes;
            $assetUser->save();
        }

        if ($assetUser) {
            // Notifying to User
            Notify::send('employee_asset_return', $assetUser);
        }
    }

    public function removAssetUser()
    {
        $request = request();
        $AssetUserid = $this->getIdFromHash($request->id);
        $assetUser = AssetUser::find($AssetUserid);
        $assetUser->delete();

        return ApiResponse::make('Delete Successfully', []);
    }
}

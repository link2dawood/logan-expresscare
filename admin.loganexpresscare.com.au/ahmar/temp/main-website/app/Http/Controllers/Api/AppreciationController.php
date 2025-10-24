<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Appreciation\IndexRequest;
use App\Http\Requests\Api\Appreciation\StoreRequest;
use App\Http\Requests\Api\Appreciation\UpdateRequest;
use App\Http\Requests\Api\Appreciation\DeleteRequest;
use App\Models\AccountEntry;
use App\Models\Appreciation;
use App\Models\Generate;

class AppreciationController extends ApiBaseController
{
    protected $model = Appreciation::class;

    public $oldAccountId = null;
    public $oldPriceAmount = null;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function modifyIndex($query)
    {
        $loggedUser = user();
        $request = request();

        if ($request->has('date') && $request->date != '') {
            $date = explode(',', $request->date);
            $startDate = $date[0];
            $endDate = $date[1];

            $query = $query->whereRaw('appreciations.date >= ?', [$startDate])
                ->whereRaw('appreciations.date <= ?', [$endDate]);
        }

        if ($loggedUser->ability('admin', 'appreciations_view')) {
            $query = $this->applyVisibility($query, 'appreciations');

            if ($request->has('user_id')) {
                $query = $query->where('appreciations.user_id', $this->getIdFromHash($request->user_id));
            }
        } else {
            $query = $query->where('appreciations.user_id', $loggedUser->id);
        }

        return $query;
    }

    public function storing(Appreciation $appreciation)
    {
        $request = request();
        $loggedUser = user();

        $appreciation->created_by = $loggedUser->id;

        if ($appreciation->letterhead_template_id && $request->letterhead_description != '') {

            $generate = new Generate();
            $generate->user_id = $appreciation->user_id;
            $generate->letterhead_template_id = $appreciation->letterhead_template_id;
            $generate->title = $request->letterhead_title;
            $generate->description = $request->letterhead_description;
            $generate->admin_id = $loggedUser->id;
            $generate->left_space = 20;
            $generate->right_space = 20;
            $generate->top_space = 20;
            $generate->bottom_space = 20;
            $generate->save();

            $appreciation->generates_id = $generate->id;
        }

        // if price_amount not entered then setup account_id to null
        $appreciation->account_id = $request->price_amount && (float) $request->price_amount > 0 ? $appreciation->account_id : null;

        return $appreciation;
    }

    public function stored(Appreciation $appreciation)
    {
        $request = request();

        if ($request->has('account_id') && $request->account_id != "" && $request->account_id != null && $appreciation->price_amount && (float) $appreciation->price_amount > 0) {
            CommonHrm::insertAccountEntries($appreciation->account_id, null, "appreciation", $appreciation->date, $appreciation->id, $appreciation->price_amount);
            CommonHrm::updateAccountAmount($appreciation->account_id);
        }

        // Notifying to User
        Notify::send('employee_on_appreciation', $appreciation);
    }

    public function updating(Appreciation $appreciation)
    {
        $request = request();
        $loggedUser = user();
        $appreciation->created_by = $loggedUser->id;

        CommonHrm::createUpdateGenerate($appreciation);

        // if price_amount not entered then setup account_id to null
        $appreciation->account_id = $request->price_amount && (float) $request->price_amount > 0 ? $appreciation->account_id : null;

        $this->oldAccountId = $appreciation->getOriginal('account_id');
        $this->oldPriceAmount = $appreciation->getOriginal('price_amount');

        return $appreciation;
    }

    public function updated(Appreciation $appreciation)
    {
        $request = request();

        // If no changes in the price
        // If price changes
        // If price removed

        if ($appreciation->price_amount && (float) $request->price_amount > 0) {
            if ($this->oldPriceAmount) {
                // Update the account entry
                AccountEntry::where('type', "appreciation")->where('appreciation_id', $appreciation->id)->update([
                    'account_id' => $appreciation->account_id,
                    'amount' => $appreciation->price_amount,
                    'date'  => $appreciation->date
                ]);
            } else {
                CommonHrm::insertAccountEntries($appreciation->account_id, $this->oldAccountId, "appreciation", $appreciation->date, $appreciation->id, $appreciation->price_amount);
            }


            if ($this->oldAccountId != $request->account_id) {
                CommonHrm::updateAccountAmount($this->oldAccountId);
            }

            CommonHrm::updateAccountAmount($appreciation->account_id);
        } else {
            AccountEntry::where('type', "appreciation")->where('appreciation_id', $appreciation->id)->delete();

            if ($this->oldAccountId) {
                CommonHrm::updateAccountAmount($this->oldAccountId);
            }
        }
    }

    public function destroyed(Appreciation $appreciation)
    {
        if ($appreciation->account_id) {
            CommonHrm::updateAccountAmount($appreciation->account_id);
        }
    }
}

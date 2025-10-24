<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Account\IndexRequest;
use App\Http\Requests\Api\Account\StoreRequest;
use App\Http\Requests\Api\Account\UpdateRequest;
use App\Http\Requests\Api\Account\DeleteRequest;
use App\Models\Account;
use Carbon\Carbon;

class AccountController extends ApiBaseController
{
	protected $model = Account::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function stored(Account $account)
	{
		$today = Carbon::now();

		CommonHrm::insertAccountEntries($account->id, null, "initial_balance", $today, $account->id, $account->initial_balance);
		CommonHrm::updateAccountAmount($account->id);
	}

	public function updated(Account $account)
	{
		$today = Carbon::now();
		CommonHrm::insertAccountEntries($account->id, null, "initial_balance", $today, $account->id, $account->initial_balance);
		CommonHrm::updateAccountAmount($account->id);
	}

	public function destroyed(Account $account)
	{

		CommonHrm::updateAccountAmount($account->id);
	}
}

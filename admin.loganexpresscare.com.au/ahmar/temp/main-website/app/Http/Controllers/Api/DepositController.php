<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Deposit\IndexRequest;
use App\Http\Requests\Api\Deposit\StoreRequest;
use App\Http\Requests\Api\Deposit\UpdateRequest;
use App\Http\Requests\Api\Deposit\DeleteRequest;
use App\Models\Deposit;

class DepositController extends ApiBaseController
{
	public $oldAccountId;
	protected $model = Deposit::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;

	public function stored(Deposit $deposit)
	{

		CommonHrm::insertAccountEntries($deposit->account_id, null, "deposit", $deposit->date_time, $deposit->id, $deposit->amount);
		CommonHrm::updateAccountAmount($deposit->account_id);
	}

	public function updating(Deposit $deposit)
	{

		$this->oldAccountId = $deposit->getOriginal('account_id');

		return $deposit;
	}

	public function updated(Deposit $deposit)
	{

		CommonHrm::insertAccountEntries($deposit->account_id, $this->oldAccountId, "deposit", $deposit->date_time, $deposit->id, $deposit->amount);
		CommonHrm::updateAccountAmount($deposit->account_id);

		if ($this->oldAccountId != $deposit->account_id) {
			CommonHrm::updateAccountAmount($this->oldAccountId);
		}
	}

	public function destroyed(Deposit $deposit)
	{

		CommonHrm::updateAccountAmount($deposit->account_id);
	}
}

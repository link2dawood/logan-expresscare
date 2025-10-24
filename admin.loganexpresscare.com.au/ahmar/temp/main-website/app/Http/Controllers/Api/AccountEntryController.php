<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\AccountEntry\IndexRequest;

use App\Models\AccountEntry;
use Carbon\Carbon;

class AccountEntryController extends ApiBaseController
{
	protected $model = AccountEntry::class;

	protected $indexRequest = IndexRequest::class;

	protected function modifyIndex($query)
	{
		$request = request();

		if ($request->has('date') && $request->date != '') {
			$date = explode(',', $request->date);

			$startDate = Carbon::parse($date[0])->toDateString();
			$endDate = Carbon::parse($date[1])->toDateString();


			$query = $query->whereRaw('account_entries.date >= ?', [$startDate])
				->whereRaw('account_entries.date <= ?', [$endDate]);
		};

		if ($request->has('account_id') && ($request->account_id != "" || $request->account_id != null)) {
			$accountId = $this->getIdFromHash($request->account_id);
			$query = $query->where('account_id', $accountId);
		};

		if ($request->has('type') && $request->type != "") {
			$attendance = $request->type;
			$query = $query->where('type', $attendance);
		};
		$query = $query->reorder()->orderBy('date', 'asc');


		return  $query;
	}
}

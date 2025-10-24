<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\Application\FrontApplicationFormRequest;
use App\Models\Application;
use Examyou\RestAPI\ApiResponse;

class FrontController extends ApiBaseController
{
	protected $model = Application::class;

	protected $frontApplicationFormRequest = FrontApplicationFormRequest::class;

	public function frontApplicationForm(FrontApplicationFormRequest $frontApplicationFormRequest)
	{
		$request = request();
		$openingId = $this->getIdFromHash($request->opening_id);

		$application = new Application();
		$application->opening_id = $openingId;
		$application->applicant_name = $request->applicant_name;
		$application->contact_email = $request->contact_email;
		$application->phone_number = $request->phone_number;
		$application->gender = $request->gender != "" ?  $request->gender : null;
		$application->date_of_birth = $request->date_of_birth != null ?  $request->date_of_birth : null;
		$application->source = $request->source;
		$application->stage =  $request->stage;
		$application->address = $request->address != "" ?  $request->address : null;
		$application->cover_letter = $request->cover_letter != "" ?  $request->cover_letter : null;
		$application->image = $request->image != null ?  $request->image : null;
		$application->resume = $request->resume  != null ?  $request->resume : null;
		$application->data_question = $request->data_question;
		$application->save();

		$data = Application::get();

		return ApiResponse::make('Success', ["data" => $data]);
	}
}

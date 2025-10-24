<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\EmployeeFields\IndexRequest;
use App\Http\Requests\Api\EmployeeFields\StoreRequest;
use App\Http\Requests\Api\EmployeeFields\UpdateRequest;
use App\Http\Requests\Api\EmployeeFields\DeleteRequest;
use App\Models\EmployeeFields;
use App\Models\FieldTypes;
use Examyou\RestAPI\ApiResponse;
use Carbon\Carbon;

class EmployeeFieldsController extends ApiBaseController
{
	protected $model = EmployeeFields::class;

	protected $indexRequest = IndexRequest::class;
	protected $storeRequest = StoreRequest::class;
	protected $updateRequest = UpdateRequest::class;
	protected $deleteRequest = DeleteRequest::class;


}

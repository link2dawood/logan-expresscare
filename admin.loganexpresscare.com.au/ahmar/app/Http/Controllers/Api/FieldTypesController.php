<?php

namespace App\Http\Controllers\Api;

use App\Classes\CommonHrm;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\FieldTypes\IndexRequest;
use App\Http\Requests\Api\FieldTypes\StoreRequest;
use App\Http\Requests\Api\FieldTypes\UpdateRequest;
use App\Http\Requests\Api\FieldTypes\DeleteRequest;
use App\Http\Requests\Api\FieldTypes\UpdateStatusRequest;
use App\Models\FieldTypes;
use App\Models\EmployeeFields;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Examyou\RestAPI\ApiResponse;

class FieldTypesController extends ApiBaseController
{
    protected $model = FieldTypes::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    // protected function modifyIndex($query)
    // {
    //     $loggedUser = user();

    //     $query = $query->where('appreciations.user_id', $loggedUser->id);
    // }

    public function stored($fieldType)
    {
        $this->storeEmployeeField($fieldType);
    }

    public function storeEmployeeField($fieldType)
    {
        $request = request();

        //Remove Employee Fields
        if ($request->has('removed_fields') && is_array($request->removed_fields)) {
            foreach ($request->removed_fields as $deleteXid) {
                $id = $this->getIdFromHash($deleteXid);
                if ($id) {
                    EmployeeFields::where('id', $id)->delete();
                }
            }
        }

        // Add/update employee fields
        foreach ($request->all_form_fields as $field) {
            $employeeField = isset($field['xid']) && $field['xid'] !== null
                ? EmployeeFields::find($this->getIdFromHash($field['xid'])) ?? new EmployeeFields()
                : new EmployeeFields();
            $employeeField->field_type_id = $fieldType->id;
            $employeeField->name = $field['name'];
            $employeeField->type = $field['type'];
            $employeeField->is_required = $field['is_required'];
            $employeeField->default_value = $field['default_value'];
            $employeeField->save();
        }
    }

    public function updated($fieldType)
    {
        $this->storeEmployeeField($fieldType);
    }

    public function updateStatus(UpdateStatusRequest $request)
    {
        $xid = $request->xid;
        $id = $this->getIdFromHash($xid);

        $formFieldName = FieldTypes::find($id);
        $formFieldName->visible_to_employee = $request->status;
        $formFieldName->save();

        return ApiResponse::make('Success', []);
    }
}

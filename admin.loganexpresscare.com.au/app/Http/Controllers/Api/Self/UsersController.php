<?php

namespace App\Http\Controllers\Api\Self;

use App\Http\Controllers\ApiBaseController;
use App\Models\StaffMember;
use App\Models\UserDocument;
use App\Models\FieldTypes;
use Examyou\RestAPI\ApiResponse;

class UsersController extends ApiBaseController
{
    public function index()
    {
        $allUsers = StaffMember::select('users.id', 'users.name', 'users.profile_image', 'users.location_id', 'users.designation_id')
            ->with(['location:id,name', 'designation:id,name'])
            ->get()->toArray();

        return ApiResponse::make('Success', $allUsers);
    }

    public function getUserDocument()
    {
        $user = user();

        $userDocuments = UserDocument::select('user_documents.*')
            ->join('employee_fields', 'user_documents.field_type_id', '=', 'employee_fields.id')
            ->join('field_types', 'employee_fields.field_type_id', '=', 'field_types.id')
            ->where('user_documents.user_id', $user->id)
            ->where('field_types.visible_to_employee', 1)
            ->get()
            ->each(function ($doc) {
                $doc->append('values_url');
            });

        return ApiResponse::make('Success', ['data' => $userDocuments]);
    }

    public function getFieldTypes()
    {
        $fieldTypes = FieldTypes::with([
            'employeeField' => function ($query) {
                $query->select('id', 'name', 'field_type_id', 'type', 'default_value');
            }
        ])->where('visible_to_employee', 1)->get();

        return ApiResponse::make('Success', ['data' => $fieldTypes]);
    }
}

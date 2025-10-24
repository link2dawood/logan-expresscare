<?php

namespace App\Http\Controllers\Api;

use App\Classes\Payrolls;
use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\SalaryGroup\IndexRequest;
use App\Http\Requests\Api\SalaryGroup\StoreRequest;
use App\Http\Requests\Api\SalaryGroup\UpdateRequest;
use App\Http\Requests\Api\SalaryGroup\DeleteRequest;
use App\Models\BasicSalaryDetails;
use App\Models\SalaryComponent;
use App\Models\SalaryGroup;
use App\Models\SalaryGroupComponent;
use App\Models\SalaryGroupUser;
use App\Models\StaffMember;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class SalaryGroupController extends ApiBaseController
{
    protected $model = SalaryGroup::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;


    public function store()
    {
        $request = request();
        $formFields = $request->form_Fields;
        $salaryComponentIds = $request->salary_component_ids;
        $userIds = $request->user_ids;

        \DB::beginTransaction();

        $this->validate();

        // Create new object
        $object = new $this->model();
        $object->fill($request->all());

        if (method_exists($this, 'storing')) {
            $object = call_user_func([$this, 'storing'], $object);
        }

        $object->save();

        $this->processData($formFields, $salaryComponentIds, $userIds, $object->id);

        \DB::commit();

        if (method_exists($this, 'stored')) {
            call_user_func([$this, 'stored'], $object);
        }

        $meta = $this->getMetaData(true);

        return ApiResponse::make("Resource created successfully", ["xid" => $object->xid], $meta);
    }

    public function update(...$args)
    {
        $request = request();
        $formFields = $request->form_Fields;
        $salaryComponentIds = $request->input('salary_component_ids', []);
        $userIds = $request->input('user_ids', []);


        \DB::beginTransaction();

        // Geting id from hashids
        $xid = last(func_get_args());
        $convertedId = Hashids::decode($xid);
        $id = $convertedId[0];

        $this->validate();

        // Get object for update
        $this->query = call_user_func($this->model . "::query");

        /** @var ApiModel $object */
        $object = $this->query->find($id);

        if (!$object) {
            throw new ResourceNotFoundException();
        }

        $object->fill(request()->all());

        if (method_exists($this, 'updating')) {
            $object = call_user_func([$this, 'updating'], $object);
        }

        $object->save();
        SalaryGroupComponent::where('salary_group_id', $object->id)->delete();
        SalaryGroupUser::where('salary_group_id', $object->id)->delete();

        $this->processData($formFields, $salaryComponentIds, $userIds, $object->id);

        $meta = $this->getMetaData(true);

        \DB::commit();

        if (method_exists($this, 'updated')) {
            call_user_func([$this, 'updated'], $object);
        }

        return ApiResponse::make("Resource updated successfully", ["xid" => $object->xid], $meta);
    }

    public function destroying(SalaryGroup $salaryGroup)
    {
        $salaryComponentIds = DB::table('salary_group_components')
            ->where('salary_group_id', $salaryGroup->id)
            ->pluck('salary_component_id');

        if ($salaryComponentIds->isNotEmpty()) {
            $userIdsFromDetails = DB::table('basic_salary_details')
                ->whereIn('salary_component_id', $salaryComponentIds)
                ->pluck('user_id')
                ->unique();

            DB::table('basic_salary_details')
                ->whereIn('salary_component_id', $salaryComponentIds)
                ->delete();
        } else {
            $userIdsFromDetails = collect();
        }

        $userIdsFromGroup = DB::table('salary_group_users')
            ->where('salary_group_id', $salaryGroup->id)
            ->pluck('user_id')
            ->unique();

        $allUserIds = $userIdsFromDetails->merge($userIdsFromGroup)->unique();

        foreach ($allUserIds as $userId) {
            $user = StaffMember::find($userId);
            if ($user) {
                $this->calculateAndUpdateUserSalary($user);
            }
        }

        return $salaryGroup;
    }

    protected function processData(array $formFields, array $salaryComponentIds, array $userIds, int $salaryGroupId)
    {
        $processedSalaryComponentIds = [];

        foreach ($formFields as $formField) {
            $salaryComponent = isset($formField['xid']) && $formField['xid'] != ''
                ? SalaryComponent::find($this->getIdFromHash($formField['xid']))
                : new SalaryComponent();

            $salaryComponent->fill([
                'monthly' => $formField['monthly'],
                'semi_monthly' => $formField['semi_monthly'],
                'weekly' => $formField['weekly'],
                'name' => $formField['name'],
                'bi_weekly' => $formField['bi_weekly'],
                'type' => $formField['type'],
                'value_type' => $formField['value_type'],
            ]);

            $salaryComponent->save();
            $processedSalaryComponentIds[] = $salaryComponent->id;
        }

        $processedSalaryComponentIds = array_merge($processedSalaryComponentIds, array_map([$this, 'getIdFromHash'], $salaryComponentIds));
        $processedSalaryComponentIds = array_unique($processedSalaryComponentIds);

        foreach ($processedSalaryComponentIds as $salaryComponentId) {
            $salaryGroupComponent = new SalaryGroupComponent();
            $salaryGroupComponent->salary_group_id = $salaryGroupId;
            $salaryGroupComponent->salary_component_id = $salaryComponentId;
            $salaryGroupComponent->save();
        }

        foreach ($userIds as $userId) {
            $salaryGroupUser = new SalaryGroupUser();
            $salaryGroupUser->user_id = $this->getIdFromHash($userId);
            $salaryGroupUser->salary_group_id = $salaryGroupId;
            $salaryGroupUser->save();


            $employeeId = $this->getIdFromHash($userId);
            $user = StaffMember::find($employeeId);
            if ($user) {
                $user->salary_group_id = $salaryGroupId;
                $user->save();
            }
        }

        $allSalaryComponentIds = array_merge(
            $processedSalaryComponentIds,
            array_map([$this, 'getIdFromHash'], $salaryComponentIds)
        );

        $allSalaryComponentIds = array_unique($allSalaryComponentIds);
        $this->updateBasicSalaryDetails($userIds, $allSalaryComponentIds, $salaryGroupId);
    }


    protected function updateBasicSalaryDetails(array $userIds, array $salaryComponentIds, int  $salaryGroupId)
    {
        $hashedUserIds = array_map([$this, 'getIdFromHash'], $userIds);
        $hashedComponentIds = $salaryComponentIds;

        $existingSalaryDetails = BasicSalaryDetails::whereIn('user_id', $hashedUserIds)
            ->whereIn('salary_component_id', $hashedComponentIds)
            ->get()
            ->keyBy(function ($item) {
                return $item->user_id . '-' . $item->salary_component_id;
            });


        BasicSalaryDetails::whereNotIn('user_id', $hashedUserIds)
            ->whereIn('salary_component_id', $hashedComponentIds)
            ->delete();

        BasicSalaryDetails::whereIn('user_id', $hashedUserIds)
            ->whereNotIn('salary_component_id', $hashedComponentIds)
            ->delete();


        foreach ($hashedUserIds as $userId) {
            foreach ($hashedComponentIds as $componentId) {
                $key = "$userId-$componentId";

                if (isset($existingSalaryDetails[$key])) {

                    $existingSalaryDetails[$key]->update([
                        'value_type' => $existingSalaryDetails[$key]->value_type,
                        'type' => $existingSalaryDetails[$key]->type,
                        'monthly' => $existingSalaryDetails[$key]->monthly,
                    ]);
                } else {
                    $salaryComponent = SalaryComponent::find($componentId);

                    if ($salaryComponent) {
                        BasicSalaryDetails::create([
                            'user_id' => $userId,
                            'salary_component_id' => $componentId,
                            'value_type' => $salaryComponent->value_type,
                            'type' => $salaryComponent->type,
                            'monthly' => $salaryComponent->monthly,
                        ]);
                    }
                }
            }
        }

        $users = StaffMember::whereIn('id', $hashedUserIds)
            ->with('basicSalaryDetails.salaryComponent')
            ->get();

        foreach ($users as $user) {
            $this->calculateAndUpdateUserSalary($user);
        }

        //this function use for when i remove user from group its salary calculate without components

        $usersInGroup = StaffMember::where('salary_group_id', $salaryGroupId)->get();
        $filteredUsers = $usersInGroup->filter(function ($user) use ($userIds) {
            return !in_array($this->getHashFromId($user->id), $userIds);
        })->pluck('id')->toArray();

        foreach ($filteredUsers as $filteredUserId) {
            $user = StaffMember::find($filteredUserId);
            $annualCtc =
                $user->annual_ctc;
            if ($user) {
                $user->salary_group_id = null;
                $user->save();

                Payrolls::updateUserSalary($filteredUserId, $annualCtc);
            }
        }
    }

    protected function calculateAndUpdateUserSalary($user)
    {
        Payrolls::updateUserSalary($user->id, $user->annual_ctc);
    }

    public function filterUser()
    {
        $request = request();
        $groupId = $request['group_id'];

        $assignedUserIds = SalaryGroupUser::pluck('user_id')->toArray();

        if ($groupId) {

            $groupAssignedUserIds = SalaryGroupUser::where('salary_group_id', $this->getIdFromHash($groupId))->pluck('user_id')->toArray();

            $unassignedUsers = StaffMember::whereNotIn('id', array_diff($assignedUserIds, $groupAssignedUserIds))->get();

            $groupAssignedUsers = StaffMember::whereIn('id', $groupAssignedUserIds)->get();

            $data = $unassignedUsers->merge($groupAssignedUsers);
        } else {
            $data = StaffMember::whereNotIn('id', $assignedUserIds)->get();
        }

        return ApiResponse::make('Success', ['data' => $data]);
    }
}

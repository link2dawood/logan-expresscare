<?php


namespace App\Models;

use Illuminate\Support\Arr;
use Vinkla\Hashids\Facades\Hashids;

class BaseModel extends ApiMainModel
{

    function __call($method, $arguments)
    {
        if (isset($this->hashableGetterFunctions) && isset($this->hashableGetterFunctions[$method])) {

            $value = $this->{$this->hashableGetterFunctions[$method]};

            if ($value) {
                $value = Hashids::encode($value);
            }

            return $value;
        }

        if (isset($this->hashableGetterArrayFunctions) && isset($this->hashableGetterArrayFunctions[$method])) {

            $value = $this->{$this->hashableGetterArrayFunctions[$method]};

            if (count($value) > 0) {
                $valueArray = [];

                foreach ($value as $productId) {
                    $valueArray[] = Hashids::encode($productId);
                }

                $value = $valueArray;
            }

            return $value;
        }

        return parent::__call($method, $arguments);
    }

    public function getXIDAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function getFinalResult()
    {
        return Hashids::encode($this->id);
    }

    public function allPermissionArray($permissionName, $selectionType = 'fields')
    {
        $permssionArray = [
            'salary_settings' => [
                'fields' => ['annual_ctc', 'salary_group', 'monthly_amount', 'annual_amount', 'ctc_value', 'special_allowances', 'net_salary', 'basic_salary'],
                'relations' => ['salaryGroup', 'basicSalaryDetails', 'salaryGroupUsers'],
            ],
            'leaves_view' => [
                'fields' => [],
                'relations' => ['leaves'],
            ],
            'assets_view' => [
                'fields' => [],
                'relations' => ['assets', 'assetUser'],
            ],
            'leave_types_view' => [
                'fields' => [],
                'relations' => ['leaveType'],
            ],
            'accounts_view' => [
                'fields' => ['initial_balance', 'balance'],
                'relations' => ['account'],
            ],
            'appreciations_view' => [
                'fields' => [],
                'relations' => ['account'],
            ],
        ];

        return $permssionArray && isset($permssionArray[$permissionName]) && isset($permssionArray[$permissionName][$selectionType]) ? $permssionArray[$permissionName][$selectionType] : null;
    }

    public function isRelationAllowedInQuery($fieldRelation)
    {
        $includeRelation = true;
        $searchRelatoinField = strpos($fieldRelation, ':') !== false ? explode(':', $fieldRelation)[0] : $fieldRelation;

        if (isset($this->permissions) && count($this->permissions) > 0) {
            $user = user();

            foreach ($this->permissions as $permission) {
                $relationNames = $this->allPermissionArray($permission, 'relations');

                if ($relationNames != null && in_array($searchRelatoinField, $relationNames) && !$this->isUserHavePermission($permission)) {
                    $includeRelation = false;
                    break;
                }
            }
        }

        return $includeRelation;
    }

    public function isFieldAllowedInQuery($fieldName)
    {
        $includeField = true;

        if (isset($this->permissions) && count($this->permissions) > 0) {
            $user = user();

            foreach ($this->permissions as $permission) {
                $allFields = $this->allPermissionArray($permission);

                if ($allFields != null && in_array($fieldName, $allFields) && !$this->isUserHavePermission($permission)) {
                    $includeField = false;
                    break;
                }
            }
        }

        return $includeField;
    }

    public function toArray()
    {
        $attributes = parent::toArray();

        if (isset($this->permissions) && count($this->permissions) > 0) {


            foreach ($this->permissions as $permission) {
                $permssionColumns = $this->allPermissionArray($permission);

                if (!$this->isUserHavePermission($permission) && $permssionColumns != null) {
                    $attributes = Arr::except($attributes, $permssionColumns);
                }
            }
        }

        return $attributes;
    }

    public function isUserHavePermission($permission)
    {
        $user = user();
        $userPermissions = $user && $user->role && $user->role->perms ? $user->role->perms->pluck('name')->toArray() : [];

        if ($user && $user->role && $user->role->name == 'admin') {
            return true;
        } else if (in_array($permission, $userPermissions)) {
            return true;
        }

        return false;
    }
}

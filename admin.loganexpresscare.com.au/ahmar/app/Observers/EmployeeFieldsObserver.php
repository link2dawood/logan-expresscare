<?php

namespace App\Observers;

use App\Models\EmployeeFields;
use App\Models\FieldTypes;

class EmployeeFieldsObserver
{
    public function saving(EmployeeFields $employeeFields)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $employeeFields->company_id = $company->id;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\EmployeeSpecificLeaveCount;

class EmployeeSpecificLeaveCountObserver
{
    public function saving(EmployeeSpecificLeaveCount $employeeSpecificLeaveCount)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $employeeSpecificLeaveCount->company_id = $company->id;
        }
    }
}

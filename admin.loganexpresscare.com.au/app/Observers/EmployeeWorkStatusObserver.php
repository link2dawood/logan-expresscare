<?php

namespace App\Observers;

use App\Models\EmployeeWorkStatus;

class EmployeeWorkStatusObserver
{
    public function saving(EmployeeWorkStatus $employeeWorkStatus)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $employeeWorkStatus->company_id = $company->id;
        }
    }
}

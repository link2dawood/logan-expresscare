<?php

namespace App\Observers;

use App\Models\SalaryGroupComponent;

class SalaryGroupComponentObserver
{
    public function saving(SalaryGroupComponent $salaryGroupComponent)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $salaryGroupComponent->company_id = $company->id;
        }
    }
}

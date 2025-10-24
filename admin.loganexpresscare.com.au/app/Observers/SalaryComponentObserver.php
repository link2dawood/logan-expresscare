<?php

namespace App\Observers;

use App\Models\SalaryComponent;

class SalaryComponentObserver
{
    public function saving(SalaryComponent $salaryComponent)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $salaryComponent->company_id = $company->id;
        }
    }
}

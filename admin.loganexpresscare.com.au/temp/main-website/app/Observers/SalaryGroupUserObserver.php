<?php

namespace App\Observers;

use App\Models\SalaryGroupUser;

class SalaryGroupUserObserver
{
    public function saving(SalaryGroupUser $salaryGroupUser)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $salaryGroupUser->company_id = $company->id;
        }
    }
}

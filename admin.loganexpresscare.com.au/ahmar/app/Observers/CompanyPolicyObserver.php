<?php

namespace App\Observers;

use App\Models\CompanyPolicy;

class CompanyPolicyObserver
{
    public function saving(CompanyPolicy $companyPolicy)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $companyPolicy->company_id = $company->id;
        }
    }
}

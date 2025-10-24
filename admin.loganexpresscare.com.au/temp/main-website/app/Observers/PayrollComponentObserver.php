<?php

namespace App\Observers;

use App\Models\PayrollComponent;

class PayrollComponentObserver
{
    public function saving(PayrollComponent $payrollComponent)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $payrollComponent->company_id = $company->id;
        }
    }
}

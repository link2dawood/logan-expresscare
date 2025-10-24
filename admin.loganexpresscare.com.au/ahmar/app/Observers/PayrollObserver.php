<?php

namespace App\Observers;

use App\Models\Payroll;

class PayrollObserver
{
    public function saving(Payroll $payroll)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $payroll->company_id = $company->id;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Payee;

class PayeeObserver
{
    public function saving(Payee $payee)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $payee->company_id = $company->id;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\PrePayment;

class PrePaymentObserver
{
    public function saving(PrePayment $prePayment)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $prePayment->company_id = $company->id;
        }
    }
}

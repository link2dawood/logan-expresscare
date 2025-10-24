<?php

namespace App\Observers;

use App\Models\Payer;

class PayerObserver
{
    public function saving(Payer $payer)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $payer->company_id = $company->id;
        }
    }
}

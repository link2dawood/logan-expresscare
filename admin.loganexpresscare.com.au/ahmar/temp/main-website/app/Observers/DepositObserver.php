<?php

namespace App\Observers;

use App\Models\Deposit;

class DepositObserver
{
    public function saving(Deposit $deposit)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $deposit->company_id = $company->id;
        }
    }
}

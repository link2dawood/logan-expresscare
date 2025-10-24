<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function saving(Account $account)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $account->company_id = $company->id;
        }
    }
}

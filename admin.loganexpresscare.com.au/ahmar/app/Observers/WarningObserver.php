<?php

namespace App\Observers;

use App\Models\Warning;

class WarningObserver
{
    public function saving(Warning $warning)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $warning->company_id = $company->id;
        }
    }
}

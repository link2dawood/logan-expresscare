<?php

namespace App\Observers;

use App\Models\DepositCategory;

class DepositCategoryObserver
{
    public function saving(DepositCategory $deposit_category)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $deposit_category->company_id = $company->id;
        }
    }
}

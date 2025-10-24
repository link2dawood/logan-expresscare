<?php

namespace App\Observers;

use  App\Models\Appreciation;

class AppreciationObserver
{
    public function saving(Appreciation $appreciation)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $appreciation->company_id = $company->id;
        }
    }
}

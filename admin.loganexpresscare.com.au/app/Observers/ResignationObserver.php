<?php

namespace App\Observers;

use App\Models\Offboarding;

class ResignationObserver
{
    public function saving(Offboarding $offboarding)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $offboarding->company_id = $company->id;
        }
    }
}

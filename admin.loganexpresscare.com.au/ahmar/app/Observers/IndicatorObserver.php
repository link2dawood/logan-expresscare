<?php

namespace App\Observers;

use App\Models\Indicator;

class IndicatorObserver
{
    public function saving(Indicator $indicator)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $indicator->company_id = $company->id;
        }
    }
}
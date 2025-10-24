<?php

namespace App\Observers;

use App\Models\WorkDuration;

class WorkDurationObserver
{
    public function saving(WorkDuration $workDuration)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $workDuration->company_id = $company->id;
        }
    }
}

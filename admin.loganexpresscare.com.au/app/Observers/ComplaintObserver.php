<?php

namespace App\Observers;

use App\Models\Complaint;

class ComplaintObserver
{
    public function saving(Complaint $complaint)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $complaint->company_id = $company->id;
        }
    }
}

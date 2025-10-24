<?php

namespace App\Observers\Self;

use App\Models\Self\SelfComplaint;

class SelfComplaintObserver
{
    public function saving(SelfComplaint $complaint)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $complaint->company_id = $company->id;
        }
    }
}

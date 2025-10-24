<?php

namespace App\Observers;

use App\Models\Feedback;

class FeedbackObserver
{
    public function saving(Feedback $feedback)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $feedback->company_id = $company->id;
        }
    }
}

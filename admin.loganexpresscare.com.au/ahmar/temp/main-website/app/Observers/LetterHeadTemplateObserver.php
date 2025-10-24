<?php

namespace App\Observers;

use App\Models\LetterHeadTemplate;

class LetterHeadTemplateObserver
{
    public function saving(LetterHeadTemplate $letterHeadTemplate)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $letterHeadTemplate->company_id = $company->id;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Generate;

class GenerateObserver
{
    public function saving(Generate $generate)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $generate->company_id = $company->id;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Form;

class FormObserver
{
    public function saving(Form $form)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $form->company_id = $company->id;
        }
    }
}

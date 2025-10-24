<?php

namespace App\Observers;

use App\Models\FieldTypes;

class FieldTypesObserver
{
    public function saving(FieldTypes $fieldTypes)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $fieldTypes->company_id = $company->id;
        }
    }
}

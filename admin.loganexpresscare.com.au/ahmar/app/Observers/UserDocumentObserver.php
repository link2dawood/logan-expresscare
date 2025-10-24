<?php

namespace App\Observers;

use App\Models\UserDocument;

class UserDocumentObserver
{
    public function saving(UserDocument $userDocument)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $userDocument->company_id = $company->id;
        }
    }
}

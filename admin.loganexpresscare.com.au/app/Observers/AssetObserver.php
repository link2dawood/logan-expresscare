<?php

namespace App\Observers;

use App\Models\Asset;

class AssetObserver
{
    public function saving(Asset $asset)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $asset->company_id = $company->id;
        }
    }
}

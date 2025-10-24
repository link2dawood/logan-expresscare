<?php

namespace App\Observers;

use App\Models\AssetType;

class AssetTypeObserver
{
    public function saving(AssetType $asset_type)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $asset_type->company_id = $company->id;
        }
    }
}

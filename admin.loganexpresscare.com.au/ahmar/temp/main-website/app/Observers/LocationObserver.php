<?php

namespace App\Observers;

use App\Models\Location;

class LocationObserver
{
    public function saving(Location $location)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $location->company_id = $company->id;
        }
    }
}

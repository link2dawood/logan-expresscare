<?php

namespace App\Observers;

use App\Models\News;

class NewsObserver
{
    public function saving(News $news)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $news->company_id = $company->id;
        }
    }
}

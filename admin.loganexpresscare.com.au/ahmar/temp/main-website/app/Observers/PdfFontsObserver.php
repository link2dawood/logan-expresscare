<?php

namespace App\Observers;

use App\Models\PdfFonts;

class PdfFontsObserver
{
    public function saving(PdfFonts $pdfFonts)
    {
        $company = company();

        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if ($company && !$company->is_global) {
            $pdfFonts->company_id = $company->id;
        }
    }
}

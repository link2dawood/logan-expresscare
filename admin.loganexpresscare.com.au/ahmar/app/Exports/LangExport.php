<?php

namespace App\Exports;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LangExport implements FromCollection,  WithHeadings
{
    use Exportable;

    private $lang;

    public function __construct($translations)
    {
        $this->lang = $translations;
    }

    public function headings(): array
    {
        return [
            'lang_key',
            'group',
            'key',
            'value',
        ];
    }

    public function collection()
    {
        return collect($this->lang)->map(function ($translation) {
            return [
                'lang_key' => $translation['lang_key'] ?? '',
                'group' => $translation['group'] ?? '',
                'key' => $translation['key'] ?? '',
                'value' => $translation['value'] ?? '',
            ];
        });
    }
}

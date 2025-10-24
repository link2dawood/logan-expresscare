<?php

namespace App\Exports;

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Collection;

class LangStaticExport implements FromCollection, WithHeadings
{
    use Exportable;

    private $langs;

    public function __construct(array $langDetails)
    {
        $this->langs = collect($langDetails ?? []);
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

    public function collection(): Collection
    {
        return $this->langs->map(function ($lang) {
            return [
                'lang_key' => $lang['lang_key'] ?? '',
                'group'    => $lang['group'] ?? '',
                'key'      => $lang['key'] ?? '',
                'value'    => $lang['value'] ?? '',
            ];
        });
    }
}

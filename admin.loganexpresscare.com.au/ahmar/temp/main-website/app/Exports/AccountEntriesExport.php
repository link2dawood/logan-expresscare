<?php

namespace App\Exports;

use App\Models\AccountEntry;
use App\Classes\Common;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AccountEntriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    private $accountId;
    private $startDate;
    private $endDate;
    private $isType;
    private $openingBalance;

    public function __construct($accountId, $startDate = null, $endDate = null, $isType = true, $openingBalance = 0)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->isType = $isType;
        $this->openingBalance = $openingBalance;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Description',
            'Credit',
            'Debit',
            'Balance',
        ];
    }

    public function collection()
    {
        $query = AccountEntry::select('date', 'type', 'amount', 'is_debit')
            ->where('account_id', $this->accountId);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        $entries = $query->orderBy('date', 'asc')->get();

        return $entries;
    }

    public function map($entry): array
    {
        static $balance = null;
        if ($balance === null) {
            $balance = $this->openingBalance;
        }
        $company = company();
        // Adjust balance based on debit or credit
        $balance = $entry->is_debit ? $balance - $entry->amount : $balance + $entry->amount;

        return [
            Common::formatDate($entry->date, $company),
            $entry->type,
            $entry->is_debit ? '' : $entry->amount,
            $entry->is_debit ? $entry->amount : '',
            $balance,
        ];
    }
}

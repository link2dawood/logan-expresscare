<?php

namespace App\Exports;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseExport implements FromCollection,  WithHeadings
{
    use Exportable;

    private $expenses;
    private $type;

    public function __construct($expenseDetails, $type = null)
    {
        $this->expenses = collect($expenseDetails);
        $this->type = $type;
    }

    public function headings(): array
    {
        $headings = [
            'Expense Type',
            'Expense Category',
            'Amount',
            'Expense Date',
            'Payment Date',
            'Payment Status',
            'Account',
        ];
        if ($this->type === 'employee_claims') {
            array_unshift($headings, 'Employee Name');
            array_unshift($headings, 'Employee Id');
        }
        return $headings;
    }

    public function collection()
    {
        return $this->expenses->map(function ($expense) {
            $expenseDate = '';
            if (!empty($expense['date_time'])) {
                $expenseDate = \Carbon\Carbon::parse($expense['date_time'])->format('d M Y');
            }
            $row = [
                'expense_type' => $expense['expense_type'] ?? '',
                'expense_category' => $expense['expenseCategory']['name'] ?? '',
                'amount' => $expense['amount'] ?? '',
                'expense_date' => $expenseDate,
                'payment_date' => $expense['payment_date'] ?? '',
                'payment_status' => $expense['status'] ?? '',
                'account' => $expense['account']['name'] ?? '',
            ];
            if ($this->type === 'employee_claims') {
                $row = array_merge([
                    'employee_id' => $expense['user']['employee_number'] ?? '',
                    'employee_name' => $expense['user']['name'] ?? '',
                ], $row);
            }
            return $row;
        });
    }
}

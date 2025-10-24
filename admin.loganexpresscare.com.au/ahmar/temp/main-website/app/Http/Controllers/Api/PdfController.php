<?php

namespace App\Http\Controllers\Api;

use App\Classes\Common;
use App\Http\Controllers\ApiBaseController;
use App\Models\Account;
use App\Models\AccountEntry;
use App\Models\Generate;
use App\Models\Holiday;
use App\Models\Lang;
use App\Models\Payroll;
use App\Models\Translation;
use App\Exports\AccountEntriesExport;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceStaticExport;
use App\Exports\ExpenseExport;
use App\Exports\LangExport;
use App\Exports\LangStaticExport;
use App\Exports\UsersStaticExport;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\CompanyPolicy;
use App\Models\Expense;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use PDF;

class PdfController extends ApiBaseController
{
    public function setPdfConfig($withHeaderFooter = true, $isExport = false)
    {
        $company = company();
        if ($isExport) {
            //export pdf spacing or margin
            $marginLeft = $withHeaderFooter ? 0 : $company->export_pdf_left_space;
            $marginRight = $withHeaderFooter ? 0 : $company->export_pdf_right_space;
            $marginTop = $withHeaderFooter ?  $company->export_pdf_top_space : $company->export_pdf_top_space;
            $marginBottom = $withHeaderFooter ? $company->export_pdf_bottom_space : $company->export_pdf_bottom_space;
        } else {
            $marginLeft = $withHeaderFooter ? 0 : $company->letterhead_left_space;
            $marginRight = $withHeaderFooter ? 0 : $company->letterhead_right_space;
            $marginTop = $withHeaderFooter ? $company->letterhead_top_space : $company->letterhead_top_space;
            $marginBottom = $withHeaderFooter ? $company->letterhead_bottom_space : $company->letterhead_bottom_space;
        }

        // Set PDF configuration
        $allFonts = [];

        if ($company->use_custom_font && $company->pdfFont && $company->pdfFont->id) {
            $allFonts[$company->pdfFont->short_name] = [
                'R'  => $company->pdfFont->file,
                'B'  => $company->pdfFont->file,
                'I'  => $company->pdfFont->file,
                'BI' => $company->pdfFont->file,
                'useOTL' => $company->pdfFont->user_kashida,
                'useKashida' => $company->pdfFont->use_otl,
            ];
        };

        config([
            'pdf.margin_left' => (int)$marginLeft,
            'pdf.margin_right' => (int)$marginRight,
            'pdf.margin_top' => (int)$marginTop,
            'pdf.margin_bottom' => (int)$marginBottom,
            'pdf.margin_header'  => (int)0,
            'pdf.margin_footer'  => (int)0,
            'pdf.auto_language_detection'  => $company->use_custom_font && $company->pdfFont && $company->pdfFont->id ? false : true,
            'pdf.custom_font_data' => $allFonts,
        ]);
    }

    public function holidayPdf()
    {
        $request = request();
        $company = company();
        $title = $request->title;
        $year = $request->year;
        $langKey = $request->has('lang') ? $request->lang : 'en';
        $holidays = Holiday::where('is_weekend', 0)->whereYear('date', $year)->orderBy('date', 'asc')->get();
        $action = $request->input('action', 'stream'); // default to stream if not provided

        $lang = Lang::where('key', $langKey)->first();
        if (!$lang) {
            $lang = Lang::where('key', 'en')->first();
        }

        $translation = Translation::where('lang_id', $lang->id)
            ->where('group', 'holiday')
            ->pluck('value', 'key')
            ->toArray();

        $margins = $this->getMargins();

        $pdfData = [
            'title' => $title,
            'company' => $company,
            'holidays' => $holidays,
            'translation' => $translation,
            'light_color' => Common::lightenHexColor($company->letterhead_header_color, 30),
            'showHeaderFooter' => true,
            'margins' => $margins,
        ];

        $this->setPdfConfig();

        $pdf = PDF::loadView('pdf.holiday', $pdfData);

        $pdfTitle = $translation && $translation['holiday_calendar'] ? $translation['holiday_calendar'] . ' ' . $year  . '.pdf' : 'holidays ' . $year . '.pdf';
        $pdfAction = $action == 'download' ? 'attachment' : 'inline';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $pdfAction . '; filename="' . $pdfTitle . '"')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function exportAccountEntries(Excel $excel)
    {
        $request = request();

        $accountId = $this->getIdFromHash($request->xid);
        $startDate = $request->date[0] ? Carbon::parse($request->date[0])->toDateString() : null;
        $endDate = $request->date[1] ? Carbon::parse($request->date[1])->toDateString() : null;
        $isType = $request->has('type') ? false : true;
        $openingBalance = $this->calculateOpeningBalance($startDate, $endDate, $accountId, $request->type ?? '');

        $export = new AccountEntriesExport($accountId, $startDate, $endDate, $isType, $openingBalance['opening_balance']);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'account_entries.csv');
    }


    public function downloadAccountEntriesExcel(Excel $excel)
    {
        $request = request();
        $accountId = $this->getIdFromHash($request->xid);
        $startDate = $request->date[0] ? Carbon::parse($request->date[0])->toDateString() : null;
        $endDate = $request->date[1] ? Carbon::parse($request->date[1])->toDateString() : null;
        $isType = $request->has('type') ? false : true;
        $openingBalance = $this->calculateOpeningBalance($startDate, $endDate, $accountId, $request->type ?? '');

        $export = new AccountEntriesExport($accountId, $startDate, $endDate, $isType, $openingBalance['opening_balance']);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::XLSX);
        }, 'account_entries.xlsx');
    }

    public function accountPdf()
    {
        $request = request();
        $company = company();

        $title = $request->title;
        $id = $this->getIdFromHash($request->xid);
        $langKey = $request->has('lang') ? $request->lang : 'en';
        $action = $request->input('action', 'stream'); // default to stream if not provided

        $account = Account::find($id);

        $statementDate = '';
        $fromDate = '';
        $endDate = '';
        $isType = true;

        $statementDate = Carbon::now()->format('Y-m-d');

        if ($request->has('date') && count($request->date) > 0) {
            $dates = $request->date;
            $startDate = Carbon::parse($dates[0])->toDateString();
            $endDate = Carbon::parse($dates[1])->toDateString();
            $fromDate = $startDate;
            if ($request->has('type') && $request->type != "") {
                $openingDetail = $this->calculateOpeningBalance($startDate, $endDate, $id, $request->type);
                $isType = false;
            } else {

                $openingDetail = $this->calculateOpeningBalance($startDate, $endDate, $id, '');
            }
        } else {
            $fromDate = $account->created_at->format('Y-m-d');
            $startDate = null;
            $endDate = $statementDate;
            if ($request->has('type') && $request->type != "") {
                $openingDetail = $this->calculateOpeningBalance(null, $endDate, $id, $request->type);
                $isType = false;
            } else {

                $openingDetail = $this->calculateOpeningBalance(null, $statementDate, $id, '');
            }
        }


        $accountEntries = AccountEntry::select(
            'account_entries.account_id',
            'account_entries.amount',
            'account_entries.is_debit',
            'account_entries.type',
            'account_entries.date',
            'account_entries.deposit_id',
            'account_entries.initial_account_id'
        )
            ->where('account_entries.account_id', $id)
            ->when($request->has('date') && count($request->date) > 0, function ($query) use ($request) {
                $dates = $request->date;

                $startDate = Carbon::parse($dates[0])->toDateString();
                $endDate = Carbon::parse($dates[1])->toDateString();

                $query->whereBetween('account_entries.date', [$startDate, $endDate]);
            })
            ->when($request->has('type') && $request->type != "", function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->orderBy('date', 'asc')
            ->with('account')
            ->get();

        $lang = Lang::where('key', $langKey)->first();
        if (!$lang) {
            $lang = Lang::where('key', 'en')->first();
        }

        $translation = Translation::where('lang_id', $lang->id)
            ->where('group', 'account')
            ->pluck('value', 'key')
            ->toArray();

        $margins = $this->getMargins();

        $pdfData = [
            'title' => $title,
            'company' => $company,
            'accountEntries' => $accountEntries,
            'translation' => $translation,
            'account' => $account,
            'openingDetail' => $openingDetail,
            'statementDate' => $statementDate,
            'fromDate' => $fromDate,
            'endDate' => $endDate,
            'isType' => $isType,
            'showHeaderFooter' => true,
            'margins' => $margins,
        ];

        $this->setPdfConfig();

        $pdf = PDF::loadView('pdf.account_entries', $pdfData);

        $pdfTitle = $translation && $translation['account_statement'] ? $translation['account_statement'] . '-' . $startDate . 'To' . $endDate  . '.pdf' : 'holidays ' . $year . '.pdf';
        $pdfAction = $action == 'download' ? 'attachment' : 'inline';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $pdfAction . '; filename="' . $pdfTitle . '"')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function payrollPdf($xid)
    {
        $request = request();
        $id = $this->getIdFromHash($xid);
        $company = company();
        $action = $request->input('action', 'stream');

        $id = $this->getIdFromHash($xid);
        $payroll = Payroll::with([
            'payrollComponents.prePayment',
            'user.department',
            'user.designation'
        ])->find($id);

        $user = $payroll->user;

        $lang = Lang::where('key', $request->lang)->first();
        if (!$lang) {
            $lang = Lang::where('key', 'en')->first();
        }
        $payrollTranslation = Translation::where('lang_id', $lang->id)
            ->where('group', 'payroll')
            ->pluck('value', 'key');

        $margins = $this->getMargins();


        $companyLogoPath = Common::getFolderPath('companyLogoPath');

        $pdfData = [
            'htmlcontent' => $request->description,
            'title' => $request->title,
            'payroll' => $payroll,
            'company' => $company,
            'showHeaderFooter' => true,
            'payrollTranslation' => $payrollTranslation,
            'margins' => $margins,
            'user' => $user
        ];

        $this->setPdfConfig();

        $pdf = PDF::loadView('pdf.payroll', $pdfData);

        $pdfTitle =  $user->name . '-' . $payroll->month . '-' .  $payroll->year  . '.pdf';
        $pdfAction = $action == 'download' ? 'attachment' : 'inline';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $pdfAction . '; filename="' . $pdfTitle . '"')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function expensePdf()
    {
        $request = request();

        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();
        $expenseType = $request->input('expense_type');
        $expenses = [];
        $company = company();
        $showHeaderFooter = $request->input('showHeaderFooter');

        $expenseQuery = Expense::with(['user', 'expenseCategory', 'account'])
            ->whereBetween('date_time', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $expenseQuery->whereIn('user_id', $realUserIds);
                }
            }
        } elseif ($request->filled('user_id')) {
            $realUserId = $this->getIdFromHash($request->input('user_id'));
            $expenseQuery->where('user_id', $realUserId);
        }

        if ($request->filled('expense_type')) {
            $expenseQuery->where('expense_type', $expenseType);
        }

        $filteredExpenses = $expenseQuery->get();

        if ($filteredExpenses->count() > 0) {
            $expenses = $filteredExpenses;
            $company = Company::find($company->id);
        }

        $lang = Lang::where('key', $request->lang)->first();
        if (!$lang) {
            $lang = Lang::where('key', 'en')->first();
        }

        $expenseTranslation = Translation::where('lang_id', $lang->id)
            ->where('group', 'expense')
            ->pluck('value', 'key');

        $noExpenseData = $expenseTranslation['no_expense_data'];

        $margins = $this->getMargins();

        $headers = [];
        $records = [];

        if (isset($expenseType) && $expenseType === 'employee_claims') {
            $headers['user'] = $expenseTranslation['user'] ?? 'User';
        }

        $headers = array_merge($headers, [
            'reference_number' => $expenseTranslation['reference_number'] ?? 'Reference Number',
            'expense_type' => $expenseTranslation['expense_type'] ?? 'Expense Type',
            'expense_category' => $expenseTranslation['expense_category'] ?? 'Expense Category',
            'amount' => $expenseTranslation['amount'] ?? 'Amount',
            'expense_date' => $expenseTranslation['expense_date'] ?? 'Expense Date',
            'payment_date' => $expenseTranslation['payment_date'] ?? 'Payment Date',
            'payment_status' => $expenseTranslation['payment_status'] ?? 'Payment Status',
            'account' => $expenseTranslation['account'] ?? 'Account',
        ]);

        $records = collect($expenses)->map(function ($expense, $index) use ($expenseType, $expenseTranslation, $company) {
            $row = [
                '#' => $index + 1,
            ];

            if (isset($expenseType) && $expenseType === 'employee_claims') {
                $row['user'] = $expense->user->name ?? '-';
            }

            $row['reference_number'] = $expense->reference_number;
            $row['expense_type'] = match ($expense->expense_type) {
                'company_claims' => $expenseTranslation['company_claims'] ?? 'Company Claims',
                'employee_claims' => $expenseTranslation['employee_claims'] ?? 'Employee Claims',
                default => '-',
            };
            $row['expense_category'] = $expense->expenseCategory->name ?? '-';
            $row['amount'] = Common::formatAmountCurrency($company->currency, $expense->amount);
            $row['expense_date'] = \Carbon\Carbon::parse($expense->date_time)->format('d M Y');
            $row['payment_date'] = $expense->payment_date ?? '-';
            $row['payment_status'] = ucfirst($expense->status);
            $row['account'] = $expense->account->name ?? '-';

            return $row;
        })->toArray();

        $pdfData = [
            'headers' => $headers,
            'htmlcontent' => $request->description,
            'title' => $request->title,
            'records' => $records,
            'company' => $company,
            'showHeaderFooter' => $showHeaderFooter,
            'margins' => $margins,
            'expenseTranslation' => $expenseTranslation,
            'expenseType' => $expenseType,
            'noData' => $noExpenseData,
        ];

        $this->setPdfConfig($showHeaderFooter, true);

        $pdf = PDF::loadView('pdf.export', $pdfData);

        $pdfTitle = 'expense.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Access-Control-Allow-Origin', '*');
    }


    public function downloadExpenseCsv(Excel $excel)
    {
        $request = request();

        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();
        $expenseType = $request->input('expense_type');

        $expenseQuery = Expense::with(['user', 'expenseCategory', 'account'])
            ->whereBetween('date_time', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $expenseQuery->whereIn('user_id', $realUserIds);
                }
            }
        }

        if ($request->filled('expense_type')) {
            $expenseQuery->where('expense_type', $expenseType);
        }

        $filteredExpenses = $expenseQuery->get();

        $export = new ExpenseExport($filteredExpenses, $expenseType);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'expenses.csv');
    }

    public function downloadExpenseExcel(Excel $excel)
    {
        $request = request();

        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();

        $expenseType = $request->input('expense_type');

        $expenseQuery = Expense::with(['user', 'expenseCategory', 'account'])
            ->whereBetween('date_time', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $expenseQuery->whereIn('user_id', $realUserIds);
                }
            }
        } elseif ($request->filled('user_id')) {
            $realUserId = $this->getIdFromHash($request->input('user_id'));
            $expenseQuery->where('user_id', $realUserId);
        }

        if ($request->filled('expense_type')) {
            $expenseQuery->where('expense_type', $expenseType);
        }

        $filteredExpenses = $expenseQuery->get();

        $export = new ExpenseExport($filteredExpenses, $expenseType);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::XLSX);
        }, 'expenses.xlsx');
    }


    public function getMargins()
    {
        $company = company();

        return [
            'left' => $company->letterhead_left_space . 'mm',
            'right' => $company->letterhead_right_space . 'mm',
            'top' => $company->letterhead_top_space . 'mm',
            'bottom' => $company->letterhead_bottom_space . 'mm',
        ];
    }

    public function generatePdf($xid = null)
    {
        $request = request();
        $showHeaderFooter = $request->has('show_header_footer') && $request->show_header_footer == 'no' ? false : true;
        $action = $request->input('action', 'stream'); // default to stream if not provided

        $company = company();

        $margins = $this->getMargins();

        if ($xid != null) {
            $id = $this->getIdFromHash($xid);
            $generate = Generate::where('id', $id)->with(['user'])->first();
            $title = $generate->title;
            $htmlcontent = $generate->description;
        } else {
            $title = $request->title;
            $htmlcontent = $request->html;
        }

        $pdfData = [
            'htmlcontent' => $htmlcontent,
            'title' => $title,
            'company' => $company,
            'showHeaderFooter' => $showHeaderFooter,
            'margins' => $margins,
        ];

        $this->setPdfConfig($showHeaderFooter);

        $pdf = PDF::loadView('pdf.generate', $pdfData);

        $pdfTitle =  'letter.pdf';
        $pdfAction = $action == 'download' ? 'attachment' : 'inline';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $pdfAction . '; filename="' . $pdfTitle . '"')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function companyPolicyPdf($xid = null)
    {
        $request = request();
        $showHeaderFooter = $request->has('show_header_footer') && $request->show_header_footer == 'no' ? false : true;
        $action = $request->input('action', 'stream'); // default to stream if not provided

        $company = company();

        $margins = $this->getMargins();

        if ($xid != null) {
            $id = $this->getIdFromHash($xid);
            $companyPolicy = CompanyPolicy::where('id', $id)->with(['location'])->first();
            $title = $companyPolicy->title;
            $htmlcontent = $companyPolicy->letter_description;
        } else {
            $title = $request->title;
            $htmlcontent = $request->html;
        }

        $pdfData = [
            'htmlcontent' => $htmlcontent,
            'title' => $title,
            'company' => $company,
            'showHeaderFooter' => $showHeaderFooter,
            'margins' => $margins,
        ];

        $this->setPdfConfig($showHeaderFooter);

        $pdf = PDF::loadView('pdf.company_policy', $pdfData);

        $pdfTitle =  'company-policy.pdf';
        $pdfAction = $action == 'download' ? 'attachment' : 'inline';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $pdfAction . '; filename="' . $pdfTitle . '"')
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function calculateOpeningBalance($date1, $date2, $id, $type)
    {
        $opening_balance = 0;
        if ($date1 == null) {
            $totalCredit = AccountEntry::where('account_entries.account_id', $id)
                ->where('account_entries.date', '<=', $date2)
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 0)
                ->sum('amount');

            $totalDebit = AccountEntry::where('account_entries.account_id', $id)
                ->where('account_entries.date', '<=', $date2)
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 1)
                ->sum('amount');

            $closing = $totalCredit - $totalDebit;
        } else {

            $totalCreditBefore = AccountEntry::where('account_entries.account_id', $id)
                ->where('account_entries.date', '<=', [$date1])
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 0)
                ->sum('amount');

            $totalCredit = AccountEntry::where('account_entries.account_id', $id)
                ->whereBetween('account_entries.date', [$date1, $date2])
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 0)
                ->sum('amount');

            $totalDebitBefore = AccountEntry::where('account_entries.account_id', $id)
                ->where('account_entries.date', '<=', [$date1])
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 1)
                ->sum('amount');
            $totalDebit = AccountEntry::where('account_entries.account_id', $id)
                ->whereBetween('account_entries.date', [$date1, $date2])
                ->when($type && ($type != "" || $type != null), function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->where('is_debit', '=', 1)
                ->sum('amount');

            $opening_balance = $totalCreditBefore - $totalDebitBefore;
            $closing = $opening_balance + $totalCredit - $totalDebit;
        }



        return [
            'opening_balance' => $opening_balance,
            'closing' => $closing,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
        ];
    }

    public function downloadAttendanceCsv(Excel $excel)
    {
        $request = request();

        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();

        $attendanceQuery = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $attendanceQuery->whereIn('user_id', $realUserIds);
                }
            }
        } elseif ($request->filled('user_id')) {
            $realUserId = $this->getIdFromHash($request->input('user_id'));
            $attendanceQuery->where('user_id', $realUserId);
        }

        $filteredAttendance = $attendanceQuery->get();

        $export = new AttendanceExport($filteredAttendance);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'attendance.csv');
    }

    public function downloadAttendanceExcel(Excel $excel)
    {
        $request = request();

        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();

        $attendanceQuery = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $attendanceQuery->whereIn('user_id', $realUserIds);
                }
            }
        } elseif ($request->filled('user_id')) {
            $realUserId = $this->getIdFromHash($request->input('user_id'));
            $attendanceQuery->where('user_id', $realUserId);
        }

        $filteredAttendance = $attendanceQuery->get();

        $export = new AttendanceExport($filteredAttendance);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::XLSX);
        }, 'attendance.xlsx');
    }


    public function attendancePdf()
    {
        $request = request();

        $title = $request->title;
        [$startDate, $endDate] = $request->input('date');
        $startDate = Carbon::parse($startDate)->toDateString();
        $endDate = Carbon::parse($endDate)->toDateString();
        $company = company();
        $showHeaderFooter = $request->input('showHeaderFooter');

        $attendanceQuery = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('user_ids')) {
            $hashedUserIds = $request->input('user_ids');

            if (is_array($hashedUserIds)) {
                $realUserIds = collect($hashedUserIds)->map(function ($hashedId) {
                    return $this->getIdFromHash($hashedId);
                })->filter()->all();

                if (!empty($realUserIds)) {
                    $attendanceQuery->whereIn('user_id', $realUserIds);
                }
            }
        } elseif ($request->filled('user_id')) {
            $realUserId = $this->getIdFromHash($request->input('user_id'));
            $attendanceQuery->where('user_id', $realUserId);
        }

        $attendances = $attendanceQuery->get();

        if ($attendances->count() > 0) {
            $company = Company::find($company->id);
        }

        $lang = Lang::where('key', $request->lang)->first();
        if (!$lang) {
            $lang = Lang::where('key', 'en')->first();
        }

        $attendanceTranslation = Translation::where('lang_id', $lang->id)
            ->where('group', 'attendance')
            ->pluck('value', 'key');

        $headers = [
            'name'             => $attendanceTranslation['name'],
            'employee_number'  => $attendanceTranslation['employee_number'],
            'date'             => $attendanceTranslation['date'],
            'clock_in'         => $attendanceTranslation['clock_in'],
            'clock_out'       => $attendanceTranslation['clock_out'],
            'status'           => $attendanceTranslation['status'],
        ];

        $noAttendanceData = $attendanceTranslation['no_attendance_data'];

        $records = collect($attendances)->map(function ($attendance, $index) {
            if (!empty($attendance['is_half_day'])) {
                $status = $attendanceTranslation['half_day'] ?? 'Half Day';
            } elseif (!empty($attendance['x_leave_type_id'])) {
                $status = $attendanceTranslation['on_leave'] ?? 'On Leave';
            } elseif (!empty($attendance['clock_in_date_time']) && !empty($attendance['clock_out_date_time'])) {
                $status = $attendanceTranslation['present'] ?? 'Present';
            } else {
                $status = $attendanceTranslation['absent'] ?? 'Absent';
            }

            return [
                '#' => $index + 1,
                'name' => $attendance['user']['name'] ?? 'N/A',
                'employee_number' => $attendance['user']['employee_number'] ?? 'N/A',
                'date' => Carbon::parse($attendance['date'])->format('Y-m-d') ?? 'N/A',
                'clock_in_time'  => $attendance['clock_in_time']
                    ? Carbon::parse($attendance['clock_in_time'])->format('H:i:s')
                    : '-',

                'clock_out_time' => $attendance['clock_out_time']
                    ? Carbon::parse($attendance['clock_out_time'])->format('H:i:s')
                    : '-',

                'status' => $status,
            ];
        })->toArray();


        $margins = $this->getMargins();

        $pdfData = [
            'title' => $title,
            'headers' => $headers,
            'records' => $records,
            'company' => $company,
            'showHeaderFooter' => $showHeaderFooter,
            'margins' => $margins,
            'noData' => $noAttendanceData,
        ];

        $this->setPdfConfig($showHeaderFooter, true);

        $pdf = PDF::loadView('pdf.export', $pdfData);
        $pdfTitle = 'attendance.pdf';

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Content-Disposition', 'attachment; filename="' . $pdfTitle . '"');
    }

    public function downLoadAttendanceStaticCsv(Excel $excel)
    {
        $attendanceData = [
            [
                'employee_number' => 'EMP-1',
                'date'            => '2025-08-05',
                'clock_in_time'   => '09:00:00',
                'clock_out_time'  => '18:00:00',
            ],
            [
                'employee_number' => 'EMP-2',
                'date'            => '2025-08-05',
                'clock_in_time'   => '09:15:00',
                'clock_out_time'  => '18:05:00',
            ],
            [
                'employee_number' => 'EMP-3',
                'date'            => '2025-08-05',
                'clock_in_time'   => '08:55:00',
                'clock_out_time'  => '17:45:00',
            ],
            [
                'employee_number' => 'EMP-4',
                'date'            => '2025-08-05',
                'clock_in_time'   => '09:10:00',
                'clock_out_time'  => '18:10:00',
            ],
        ];

        $export = new AttendanceStaticExport($attendanceData);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'attendance.csv');
    }

    public function downloadUsersStaticCsv(Excel $excel)
    {
        $usersData = [
            [
                'name'            => 'User 1',
                'employee_number' => 'Cod-001',
                'email'           => 'user1@example.com',
                'phone'           => '9000000001',
                'allow_login'     => 1,
                'status'          => 'active',
                'gender'          => 'male',
                'joining_date'    => '2019-03-10',
            ],
            [
                'name'            => 'User 2',
                'employee_number' => 'Cod-002',
                'email'           => 'user2@example.com',
                'phone'           => '9000000002',
                'allow_login'     => 0,
                'status'          => 'inactive',
                'gender'          => 'male',
                'joining_date'    => '2022-04-09',
            ],
            [
                'name'            => 'User 3',
                'employee_number' => 'Cod-003',
                'email'           => 'user3@example.com',
                'phone'           => '9000000003',
                'allow_login'     => 1,
                'status'          => '',
                'gender'          => '',
                'joining_date'    => '2021-08-23',
            ],
            [
                'name'            => 'User 4',
                'employee_number' => 'Cod-004',
                'email'           => 'user4@example.com',
                'phone'           => '9000000004',
                'allow_login'     => 0,
                'status'          => '',
                'gender'          => 'male',
                'joining_date'    => '2016-08-15',
            ],
            [
                'name'            => 'User 5',
                'employee_number' => 'Cod-005',
                'email'           => 'user5@example.com',
                'phone'           => '9000000005',
                'allow_login'     => 1,
                'status'          => 'active',
                'gender'          => '',
                'joining_date'    => '2023-02-02',
            ],
        ];


        $export = new UsersStaticExport($usersData);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'users.csv');
    }

    public function downloadLangsStaticCsv(Excel $excel)
    {
        $langsArray = [
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'enabled', 'value' => 'Enabled'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'disabled', 'value' => 'Disabled'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'working', 'value' => 'Working'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'not_working', 'value' => 'Not Working'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'id', 'value' => 'Id'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'action', 'value' => 'Action'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'placeholder_default_text', 'value' => 'Please Enter {0}'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'placeholder_social_text', 'value' => 'Please Enter {0} Url'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'placeholder_search_text', 'value' => 'Search By {0}'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'select_default_text', 'value' => 'Select {0}...'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'create', 'value' => 'Create'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'edit', 'value' => 'Edit'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'update', 'value' => 'Update'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'cancel', 'value' => 'Cancel'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'delete', 'value' => 'Delete'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'success', 'value' => 'Success'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'error', 'value' => 'Error'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'yes', 'value' => 'Yes'],
            ['lang_key' => 'en', 'group' => 'common', 'key' => 'no', 'value' => 'No'],
        ];

        $export = new LangStaticExport($langsArray);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'langs.csv');
    }

    public function downloadLangCsv(Excel $excel, $id)
    {
        $id = $this->getIdFromHash($id);

        $translations = Translation::join('langs', 'langs.id', '=', 'translations.lang_id')
            ->select(
                'langs.key as lang_key',
                'translations.group',
                'translations.key',
                'translations.value'
            )
            ->where('langs.id', $id)
            ->get()
            ->toArray();

        $export = new LangExport($translations);

        return response()->streamDownload(function () use ($export, $excel) {
            echo $excel->raw($export, Excel::CSV);
        }, 'attendance.csv');
    }
}

@extends('layouts.pdf')

@section('styles')
    <style>
        .letterContent p {
            margin: 0px;
            white-space: pre;
        }

        .letterContent table {
            margin: auto;
            border-collapse: collapse;
            width: 100%;
        }

        .letterContent table td,
        .letterContent table th {
            padding: 8px;
            text-align: center;
        }

        .table-text {
            color: #000;
        }

        .table-text .text-position {
            text-align: left
        }
    </style>
@endsection

@section('content')
    <div style="width: 100%;font-weight:bold;font-size:16px;">
        <table cellpadding="0" cellspacing="0" style="width: 100%;border-style:none;" class="letterContent">
            <tr>
                <td style="text-align: left;font-size:18px;font-weight:bold;border-style:none;">
                    {{ $payrollTranslation['pay_slip_for_duration'] }}
                    {{ \Carbon\Carbon::create()->month($payroll->month ?? 1)->format('F') }}
                    {{ $payroll->year ?? $payrollTranslation['year_not_available'] }}
                </td>
                <td style="text-align: left;font-size:18px;font-weight:bold;border-style:none;">
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 100%; margin-top:15px;margin-bottom:15px">
        <table cellpadding="0" cellspacing="0" style="width: 100%;" class="letterContent">
            <tr>
                <td style="text-align: left; width: 40%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['employee_id'] . ':' }}</span>
                    <span>{{ optional($payroll->user)->employee_number ?? 'N/A' }}</span>
                </td>
                <td style="text-align: left; width: 30%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['name'] . ':' }}</span>
                    <span>{{ optional($payroll->user)->name ?? 'N/A' }}</span>
                </td>
                <td style="text-align: right; width: 30%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['salary_slip'] . '#' . ':' }}</span>
                    <span>{{ '9' }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: left; width: 40%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['department'] . ':' }}</span>
                    <span>{{ optional($payroll->user)->department->name ?? 'N/A' }}</span>
                </td>
                <td style="text-align: left; width: 30%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['designation'] . ':' }}</span>
                    <span>{{ optional($payroll->user)->designation->name ?? 'N/A' }}</span>
                </td>
                <td style="text-align: right; width: 30%;">
                    <span style="font-weight: bold">{{ $payrollTranslation['joining_date'] . ':' }}</span>
                    <span>{{ optional($payroll->user)->joining_date ? \Carbon\Carbon::parse(optional($payroll->user)->joining_date)->format('d-m-Y') : 'N/A' }}</span>
                </td>
            </tr>
        </table>

    </div>

    @php
        // Calculate the total earnings
        $filteredEarningComponents = collect($payroll->payrollComponents)->whereIn('type', [
            'custom_earning',
            'earnings',
        ]);

        $totalMonthlyEarning = $filteredEarningComponents->sum('monthly_value') ?? 0;

        //Calculate special allowance
        $specialAllowance = $user->monthly_amount - $payroll->basic_salary - $totalMonthlyEarning;

        //earning components
        $additionalEarnings = collect($payroll->payrollComponents)->where('type', 'additional_earning')->values();

        $totalAdditionalEarning = $additionalEarnings->sum('monthly_value') ?? 0;

        $earningsComponents = collect($payroll->payrollComponents)
            ->whereIn('type', ['earnings', 'custom_earning'])
            ->values();

        //gross earnings sum
        $grossEarnings = round(
            $payroll->basic_salary + $specialAllowance + $totalMonthlyEarning + $totalAdditionalEarning,
            2,
        );

        //deductions components
        $deductionComponents = collect($payroll->payrollComponents)
            ->groupBy('type')
            ->only(['deductions', 'pre_payments', 'custom_deduction']);

        //sum of deduction components
        $filteredDeductionComponents = collect($payroll->payrollComponents)->whereIn('type', [
            'custom_deduction',
            'deductions',
            'pre_payments',
        ]);

        $totalMonthlyDeductions = $filteredDeductionComponents->sum(function ($component) {
            return $component['monthly_value'] ?? 0;
        });
    @endphp

    <div style="width: 100%;">
        <div style="width: 49%; float: left; margin-right: 10px;">
            <table style="width: 100%; margin-top: 10px; margin-bottom: 10px;">
                <tr style="background-color: #f0f0f0;">
                    <th class="table-text" style="text-align: left;">{{ $payrollTranslation['earnings'] }}</th>
                    <th class="table-text" style="text-align: right;">{{ $payrollTranslation['amount'] }}</th>
                </tr>
                <tr>
                    <td class="table-text text-position" style="text-align: left">{{ $payrollTranslation['basic_salary'] }}
                    </td>
                    <td class="table-text" style="text-align: right;">
                        {{ App\Classes\Common::formatAmountCurrency($company->currency, $payroll->basic_salary) }}</td>
                </tr>
                {{-- Regular Earnings and Custom Earnings --}}
                @if ($earningsComponents->isNotEmpty())
                    @foreach ($earningsComponents as $component)
                        <tr>
                            <td class="table-text text-position" style="text-align: left">{{ $component->name }}</td>
                            <td class="table-text" style="text-align: right;">
                                {{ App\Classes\Common::formatAmountCurrency($company->currency, $component->monthly_value) }}
                            </td>
                        </tr>
                    @endforeach
                @endif

                @if ($specialAllowance > 0)
                    <tr>
                        <td class="table-text text-position" style="text-align: left">
                            {{ $payrollTranslation['special_allowances'] }}
                        </td>
                        <td class="table-text" style="text-align: right;">
                            {{ App\Classes\Common::formatAmountCurrency($company->currency, $specialAllowance) }}
                        </td>
                    </tr>
                @endif

                {{-- Additional Earnings --}}
                @if ($additionalEarnings->isNotEmpty())
                    @foreach ($additionalEarnings as $component)
                        <tr>
                            <td class="table-text text-position" style="text-align: left">{{ $component->name }}</td>
                            <td class="table-text" style="text-align: right;">
                                {{ App\Classes\Common::formatAmountCurrency($company->currency, $component->monthly_value) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
        <div style="width: 49%; float: right;margin-left:10px">
            <table style="width: 100%; margin-top: 10px; margin-bottom: 10px;">
                <tr style="background-color: #f0f0f0;">
                    <th class="table-text" style="text-align: left">{{ $payrollTranslation['deductions'] }}</th>
                    <th class="table-text" style="text-align: right;">{{ $payrollTranslation['amount'] }}</th>
                </tr>
                @foreach ($deductionComponents as $type => $components)
                    @foreach ($components as $component)
                        <tr>
                            <td class="table-text" style="color: black;text-align: left">
                                {{ $component['name'] }}
                            </td>
                            <td class="table-text" style="color: black;text-align: right">
                                {{ App\Classes\Common::formatAmountCurrency($company->currency, $component['monthly_value']) }}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </table>
        </div>
    </div>
    <div style="width: 100%;">
        <div style="width: 49%; float: left;">
            <table style="width: 100%; margin-bottom: 10px;">
                <tr>
                    <th class="table-text" style="text-align: left;">{{ $payrollTranslation['gross_earnings'] }}</th>
                    <th class="table-text" style="text-align: right;">
                        {{ App\Classes\Common::formatAmountCurrency($company->currency, $grossEarnings) }}</th>
                </tr>
            </table>
        </div>
        <div style="width: 49%; float: right;">
            <table style="width: 100%;">
                <tr>
                    <th class="table-text" style="text-align: left;width: 50%">
                        {{ $payrollTranslation['total_deductions'] }}
                    </th>
                    <th class="table-text" style="text-align: right;width: 50%">
                        {{ App\Classes\Common::formatAmountCurrency($company->currency, $totalMonthlyDeductions) }}</th>
                </tr>
            </table>
        </div>
    </div>
    <div style="width: 100%; margin-top: 10px;">
        <table style="width: 100%; margin-top: 10px; margin-bottom: 10px;">
            <tr style="background-color: #f0f0f0;">
                <th class="table-text" style="text-align: left;">{{ $payrollTranslation['reimbursements'] }}</th>
                <th class="table-text" style="text-align: right;">{{ $payrollTranslation['amount'] }}</th>
            </tr>
            <tr>
                <td class="table-text" style="text-align: left;">{{ $payrollTranslation['expense_claim'] }}</td>
                <td class="table-text" style="text-align: right;">
                    {{ App\Classes\Common::formatAmountCurrency($company->currency, $payroll->expense_amount ?? 0, 2) }}
                </td>
            </tr>
        </table>
    </div>
    <div style="width: 100%; border-bottom: 1px solid #000; margin-top: 10px;"></div>
    <div style="width: 100%; margin-top: 10px;text-align:center">
        <span style="font-weight:bold;font-size:25px">{{ $payrollTranslation['net_salary'] . ':' }}</span>&nbsp;&nbsp;<span
            style="font-size:25px">{{ App\Classes\Common::formatAmountCurrency($company->currency, $payroll->net_salary) }}</span>
    </div>
    <div style="width: 100%; margin-top: 10px;text-align:center;font-size:16px">
        <span>{{ $payrollTranslation['net_salary'] . ' = ' . $payrollTranslation['gross_earnings'] . ' - ' . $payrollTranslation['total_deductions'] . ' + ' . $payrollTranslation['reimbursements'] }}</span>
    </div>
    </div>
@endsection

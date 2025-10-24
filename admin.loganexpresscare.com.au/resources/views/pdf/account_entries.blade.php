@extends('layouts.pdf')

@section('styles')
<style>
    .letterContent p {
        margin: 0px;
        white-space: pre;
    }

    .letterContent table {
        margin: auto;
        border-style: none;
        border-collapse: collapse;
        width: 100%;
    }

    .letterContent table td,
    .letterContent table th {
        border-style: none;
        padding: 8px;
        text-align: center;
        font-size: 12px;
    }

</style>
@endsection
    @section('content')

    <h2 style="text-align: center;">{{ $title }}</h2>

    <div style="width: 100%;">
        <table cellpadding="0" cellspacing="0" style="width: 100%;" class="letterContent">
            <tr>
                <td style="text-align: left;font-size:12px;">
                    <p>   {{ $translation['name'].':' }} </p><br />
                    <p>  {{ $translation['account_number'].':' }} </p><br />
                    <p>  {{ $translation['branch_code'].':' }} </p><br />
                    <p>  {{ $translation['branch_address']. ':' }}</p><br/>
                    <p> {{ $translation['statement_period']. ':' }} </p>
                </td>
                <td style="text-align: left;font-size:12px;">
                    <p>    {{ $account->name }} </p><br />
                    <p> {{ $account->account_number }} </p><br />
                    <p>  {{ $account->branch_code }} </p><br />
                    <p>  {{ $account->branch_address }}</p><br/>
                    @if($endDate)
                    <p> {{ App\Classes\Common::formatDate($fromDate,$company) .' '. ' To '. ' ' .App\Classes\Common::formatDate($endDate,$company)}}</p>
                    @else
                    <p> {{ App\Classes\Common::formatDate($fromDate,$company) .' '. ' To '. ' ' .App\Classes\Common::formatDate($statementDate,$company)}}</p>
                    @endif
                </td>
                <td style="text-align: right;font-size:12px;">
                    <p>   {{ $translation['statement_date'].':' }}  </p> <br />
                    @if($isType == true)
                    <p>   {{ $translation['opening_balance'].':' }} </p><br />
                    @endif
                    <p>   {{ $translation['total_credit'].':' }} </p><br />
                    <p>  {{ $translation['total_debit'].':' }} </p><br />
                    @if($isType == true)
                    <p>  {{ $translation['closing_balance']. ':' }}</p>
                    @endif
                </td>
                <td style="text-align: right;font-size:12px;">
                    <p>   {{ App\Classes\Common::formatDate($statementDate,$company) }} </p> <br />
                    @if($isType == true)
                    <p>   {{ App\Classes\Common::formatAmountCurrency($company->currency, $openingDetail['opening_balance']) }} </p><br />
                    @endif
                    <p>   {{ App\Classes\Common::formatAmountCurrency($company->currency, $openingDetail['total_credit']) }} </p><br />
                    <p>  {{ App\Classes\Common::formatAmountCurrency($company->currency, $openingDetail['total_debit']) }} </p><br />
                    @if($isType == true)
                    <p>  {{ App\Classes\Common::formatAmountCurrency($company->currency, $openingDetail['closing']) }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>



    <h3 style="text-align: left;padding-top:10px;color:rgb(248, 122, 76)">{{ $translation['transactions'] }}</h3>



        <table cellpadding="4px" cellspacing="0" style="width:100%;">
            <thead>
                <tr style="background-color: rgb(246, 246, 246);" >

                    <th style="border-top: 1px solid;border-bottom: 1px solid;font-weight:bold;font-size:14px;text-align:left">
                        {{ $translation['date'] }}
                    </th>
                    <th style="border-top: 1px solid;border-bottom: 1px solid;font-weight:bold;font-size:14px;text-align:left">
                        {{ $translation['description'] }}
                    </th>
                     <th style="border-top: 1px solid;border-bottom: 1px solid;font-weight:bold;font-size:14px;text-align:left">
                        {{ $translation['credit'] }}
                    </th>
                    <th style="border-top: 1px solid;border-bottom: 1px solid;font-weight:bold;font-size:14px;text-align:left">
                        {{ $translation['debit'] }}
                     </th>
                     @if($isType == true)
                     <th style="border-top: 1px solid;border-bottom: 1px solid;font-weight:bold;font-size:14px;text-align:right">
                        {{ $translation['balance'] }}
                     </th>
                     @endif

                </tr>
            </thead>
            <tbody >

                @if (count($accountEntries) > 0)
                @php($totalOpening = $openingDetail['opening_balance'])
                @php($color = 2)

            @foreach($accountEntries as $item)
            @php($totalOpening = $item->is_debit == 1?$totalOpening - $item->amount:$totalOpening + $item->amount)
                @php($color++)

             <tr style="background-color: {{ $color % 2 == 0 ? 'rgb(246, 246, 246)' : 'white' }};">
                    <td style="border-bottom: 1px solid;text-align:left;">{{App\Classes\Common::formatDate($item->date,$company)}}</td>
                    <td style="border-bottom: 1px solid;text-align:left;">{{ $translation[$item->type]}}</td>
                    @if($item->is_debit == 0)
                    <td style="border-bottom: 1px solid;text-align:left;">{{  App\Classes\Common::formatAmountCurrency($company->currency, $item->amount)}}</td>
                    <td style="border-bottom: 1px solid;text-align:left;">{{'--'}}</td>
                    @else
                    <td style="border-bottom: 1px solid;text-align:left;">{{'--'}}</td>
                    <td style="border-bottom: 1px solid;text-align:left;">{{  App\Classes\Common::formatAmountCurrency($company->currency, $item->amount)}}</td>
                    @endif
                    @if($isType == true)
                    <td style="border-bottom: 1px solid;text-align:right;">{{  App\Classes\Common::formatAmountCurrency($company->currency, $totalOpening)}}</td>
                    @endif
                </tr>

                @endforeach
                 @endif
            </tbody>

        </table>
        @endsection


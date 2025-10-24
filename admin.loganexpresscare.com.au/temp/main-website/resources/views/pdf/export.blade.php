@extends('layouts.pdf')

@section('styles')
    <style>
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
        }

        .letterContent p {
            line-height: {{ $company->export_pdf_line_height ? $company->export_pdf_line_height : 20 }}px;
            margin: 0px;
            white-space: pre;
        }

        .letterContent table {
            margin: auto;
            border-collapse: collapse;
            width: 100%;
            font-size: {{ $company->export_pdf_font_size ? $company->export_pdf_font_size : 12 }}px;
            line-height: {{ $company->export_pdf_line_height ? $company->export_pdf_line_height : 20 }}px;
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
    <div class="container">
        <table border="1" cellspacing="0" cellpadding="8" style="width:100%; border-collapse: collapse;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th>#</th>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
            <tbody>
                @forelse ($records as $record)
                    <tr>
                        @foreach ($record as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="text-center">
                            {{ $noData }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </tbody>

        </table>
    </div>
@endsection

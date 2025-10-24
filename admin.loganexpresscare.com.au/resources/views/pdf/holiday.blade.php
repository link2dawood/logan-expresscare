@extends('layouts.pdf')

@section('styles')

<style>
    .letterContent p {
        line-height: {{ $company->holiday_pdf_line_height?$company->holiday_pdf_line_height:40}}px;
        margin: 0px;
        white-space: pre;
    }

    .letterContent table {
        margin: auto;
        border-collapse: collapse;
        width: 100%;
        font-size: {{ $company->holiday_pdf_font_size? $company->holiday_pdf_font_size:20}}px;
        line-height: {{ $company->holiday_pdf_line_height?$company->holiday_pdf_line_height:40}}px;
    }

    .letterContent table td,
    .letterContent table th {
        padding: 8px 35px;
        text-align: center;
        font-weight: 800;
    }
</style>
@endsection

@section('content')

@if($company->letterhead_title_show_in_pdf)
<h2 style="text-align: center;">{{ $title }}</h2>
@endif

<table style="border: 1px solid {{$light_color}}">
    <tbody>
        @foreach ($holidays as $holiday)
        <tr>
            <td
                style="background-color: {{$light_color}};color: white;border-bottom: 1px solid @if($loop->last) {{$light_color}} @else white @endif;border-top: 1px solid @if($loop->first) {{$light_color}} @else white @endif;border-left: 1px solid {{$light_color}};border-right: 1px solid {{$light_color}};">
                <b>{{ \Carbon\Carbon::parse($holiday->date)->format('d') }}</b>
            </td>
            <td style="border: 1px solid {{$light_color}}">{{ \Carbon\Carbon::parse($holiday->date)->format('F') }}</td>
            <td style="border: 1px solid {{$light_color}}">{{ $holiday->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

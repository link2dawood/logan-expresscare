@extends('layouts.pdf')

@section('styles')

    <style>
        .letterContent table {
            margin: auto;
            border-collapse: collapse;
            width: 100%;
            font-size: {{ $company->generate_pdf_font_size? $company->generate_pdf_font_size: 17}}px;
            line-height: {{ $company->generate_pdf_line_height?$company->generate_pdf_line_height:28}}px;
        }
    </style>
    @endsection

@section('content')

@if($company->letterhead_title_show_in_pdf)
<h2 style="text-align: center;">{{ $title }}</h2>
@endif


    {!! $htmlcontent !!}



@endsection


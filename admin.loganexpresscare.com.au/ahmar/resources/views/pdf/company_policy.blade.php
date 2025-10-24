@extends('layouts.pdf')

@section('content')

@if($company->letterhead_title_show_in_pdf)
<h2 style="text-align: center;">{{ $title }}</h2>
@endif

    {!! $htmlcontent !!}

@endsection


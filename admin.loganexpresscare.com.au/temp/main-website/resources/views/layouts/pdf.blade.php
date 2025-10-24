<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>

    @php
        $fontFamily =
            $company->use_custom_font && $company->pdfFont && $company->pdfFont->id
                ? "'{$company->pdfFont->short_name}', sans-serif"
                : 'Arial, sans-serif';
    @endphp

    @php
        $generatePdfFontSize = $company->generate_pdf_font_size ? $company->generate_pdf_font_size : 17;
        $generatePdfLineHeight = $company->generate_pdf_line_height ? $company->generate_pdf_line_height : 28;
    @endphp

    <style>
        body {
            font-family: {!! $fontFamily !!};
            margin: 0;
            padding: 0;
        }
    </style>

    @if ($showHeaderFooter)
        <style>
            table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .text-right {
                text-align: right;
                color: #FFFFFF;
                font-size: 14px;
            }

            .company-title {
                font-weight: bold;
                font-size: 16px;
            }

            @page {
                header: html_EveryPageHeader;

                @if ($company->letterhead_show_company_address)
                    footer: html_EveryPageFooter;
                @endif
            }
        </style>
    @endif

    <style>
        .letterContent {
            font-size: {{ $generatePdfFontSize }}px;
        }

        .letterContent p {
            font-size: {{ $generatePdfFontSize }}px;
            margin: 0px;
            padding: 0px;
            white-space: pre;

            line-height: {{ $generatePdfLineHeight }}px;
        }

        .letterContent h1 {
            font-size: 32px;
        }

        .letterContent table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .letterContent td {
            height: 25px;
            border: 1px solid #000;
            padding: 8px;
            border-collapse: collapse;
        }

        .ql-align-center {
            text-align: center;
        }

        .ql-align-left {
            text-align: left;
        }

        .ql-align-right {
            text-align: right;
        }

        .ql-align-justify {
            text-align: justify;
        }

        .ql-editor h1 {
            font-size: 2em;
        }

        .ql-size-large {
            font-size: 2em;
        }

        .letterContent h1 {
            font-size: 2em;
        }

        .letterContent h2 {
            font-size: 1.5em;
        }

        .letterContent h3 {
            font-size: 1.17em;
        }

        .letterContent h4 {
            font-size: 1em;
        }

        .letterContent h5 {
            font-size: 0.83em;
        }

        .letterContent h6 {
            font-size: 0.67em;
        }

        .letterContent strong {
            font-weight: bold;
        }

        .letterContent sub {
            bottom: -0.25em;
            position: relative;
            font-size: 75%;
            line-height: 0;
            vertical-align: baseline;
        }

        .letterContent sup {
            top: -0.5em;
            position: relative;
            font-size: 75%;
            line-height: 0;
            vertical-align: baseline;
        }

        .ql-direction-rtl {
            direction: rtl;
        }

        .ql-size-small {
            font-size: 0.75em;
        }

        .ql-size-large {
            font-size: 1.5em;
        }

        .ql-size-huge {
            font-size: 2.5em;
        }
    </style>

    @yield('styles')
</head>

<body>
    @if ($showHeaderFooter)
        <htmlpageheader name="EveryPageHeader">
            <table cellpadding="0" cellspacing="0">
                <tr class="top" style="background-color: {{ $company->letterhead_header_color }}">
                    <td>
                        <img src="{{ $company->dark_logo_public_url }}"
                            style="width: 180px; margin-top: 30px;margin-bottom: 30px;margin-left: {{ $margins['left'] }};" />
                    </td>
                    <td colspan="2">
                        <table
                            style="width: 100%; margin-top: 10px;margin-bottom: 10px;margin-right: {{ $margins['right'] }};">
                            <tr>
                                <td class="text-right">
                                    <span class="company-title">
                                        @if ($company->letterhead_show_company_name)
                                            {{ $company->name }} <br />
                                        @endif
                                    </span>
                                    <span>
                                        @if ($company->letterhead_show_company_address)
                                            {{ $company->address }} <br />
                                        @endif
                                        @if ($company->letterhead_show_company_email)
                                            {{ $company->email }} <br />
                                        @endif
                                        @if ($company->letterhead_show_company_phone)
                                            {{ $company->phone }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </htmlpageheader>
    @endif

    <div class="letterContent"
        @if ($showHeaderFooter) style="margin-left: {{ $margins['left'] }};margin-right: {{ $margins['right'] }};" @endif>
        @yield('content')
    </div>

    @if ($showHeaderFooter && $company->letterhead_show_company_address)
        <htmlpagefooter name="EveryPageFooter">
            <div
                style="margin-top: 4%;position: fixed;width:100%;right:0px;bottom:0px;color:white; background-color: {{ $company->letterhead_header_color }}">
                <p style="padding-bottom:10px;text-align:center">{{ $company->address }}</p>
            </div>
        </htmlpagefooter>
    @endif

</body>

</html>

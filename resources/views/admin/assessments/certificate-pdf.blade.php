@php
use Carbon\Carbon;

$mulai = Carbon::parse($student->tanggal_masuk)->translatedFormat('d F Y');
$selesai = Carbon::parse($student->tanggal_selesai)->translatedFormat('d F Y');
$hariIni = $today->translatedFormat('d F Y');
@endphp

<!doctype html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            size: 330mm 210mm;
            /* F4 LANDSCAPE */
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 330mm;
            height: 210mm;
        }

        /* wrapper 1 halaman */
        .page {
            position: relative;
            width: 330mm;
            height: 210mm;
            overflow: hidden;
            font-family: 'DejaVu Sans', sans-serif;
            color: #111;

            page-break-after: always;
        }

        .page:last-child {
            page-break-after: never;
        }

        /* background sertifikat */
        .page--sertifikat {
            background-image: url("{{ public_path('sertifikat/background.png') }}");
            background-repeat: no-repeat;
            background-position: left top;
            background-size: 330mm 210mm;
        }

        /* cegah pemisahan internal */
        * {
            box-sizing: border-box;
            page-break-inside: avoid;
        }
    </style>

</head>

<body>

    {{-- HALAMAN 1 --}}
    <div class="page page--sertifikat page-break">
        @include('admin.assessments.sertifikat1-svg')
    </div>

    {{-- HALAMAN 2 --}}
    <div class="page">
        @include('admin.assessments.sertifikat2-svg')
    </div>

</body>

</html>
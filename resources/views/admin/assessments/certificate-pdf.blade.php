@php
use Carbon\Carbon;

$mulai   = Carbon::parse($student->tanggal_masuk)->translatedFormat('d F Y');
$selesai = Carbon::parse($student->tanggal_selesai)->translatedFormat('d F Y');
$hariIni = $today->translatedFormat('d F Y');

// === INSTANSI MAHASISWA (yang kamu sudah buat auto font) ===
$instansiText = trim((string) ($student->instansi ?? ''));
$len = mb_strlen($instansiText, 'UTF-8');

if ($len <= 20) {
    $instansiFont = 24;
} elseif ($len <= 28) {
    $instansiFont = 22;
} elseif ($len <= 36) {
    $instansiFont = 20.5;
} elseif ($len <= 44) {
    $instansiFont = 19.5;
} else {
    $instansiFont = 18.5;
}

$instansiStyle = 'display:block;margin-top:2px;font-weight:700;line-height:1.15;white-space:nowrap;font-size:'.$instansiFont.'px;';
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 330mm 210mm;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        .page {
            position: relative;
            width: 330mm;
            height: 210mm;
            overflow: hidden;
            font-family: "DejaVu Sans", sans-serif;
            color: #111;
        }

        .page-break {
            page-break-after: always;
        }

        * {
            box-sizing: border-box;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- ================= PAGE 1 ================= --}}
    <div class="page page-break">

        {{-- BACKGROUND --}}
        <img
            src="{{ public_path('sertifikat/sertifikat.png') }}"
            style="position:absolute; top:0; left:0; width:330mm; height:210mm; image-rendering:auto;"
            alt="Sertifikat Background"
        >

        {{-- KONTEN PAGE 1 --}}
        <div style="position:absolute; inset:0;">
            @include('admin.assessments.sertifikat1-svg', [
                'student' => $student,
                'assessment' => $assessment,
                'hasilLabel' => $hasilLabel,
                'mulai' => $mulai,
                'selesai' => $selesai,
                'hariIni' => $hariIni,
                'instansiText' => $instansiText,
                'instansiStyle' => $instansiStyle,
                'about' => $about,
            ])
        </div>

    </div>

    {{-- ================= PAGE 2 ================= --}}
    <div class="page">

        {{-- KONTEN PAGE 2 --}}
        <div style="position:absolute; inset:0;">
            @include('admin.assessments.sertifikat2-svg', [
                'student' => $student,
                'assessment' => $assessment,
                'hariIni' => $hariIni,
                'about' => $about,
            ])
        </div>

    </div>

</body>
</html>

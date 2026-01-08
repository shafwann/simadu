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

  {{-- PAGE 1 --}}
  <div class="page page-break">

    {{-- BACKGROUND HD --}}
    <img
      src="{{ public_path('sertifikat/sertifikat.png') }}"
      style="
        position:absolute;
        top:0;
        left:0;
        width:330mm;
        height:210mm;
        image-rendering: auto;
      "
      alt="Sertifikat Background"
    >

    {{-- KONTEN --}}
    <div style="position:absolute; inset:0;">
      @include('admin.assessments.sertifikat1-svg')
    </div>

  </div>

  {{-- PAGE 2 --}}
  <div class="page">
    @include('admin.assessments.sertifikat2-svg')
  </div>

</body>
</html>

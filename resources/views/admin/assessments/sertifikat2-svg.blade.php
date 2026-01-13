
@php
$rows = [
    [1,'Etika dan Kedisiplinan',$assessment->aspek_1],
    [2,'Kemampuan Berkomunikasi dan Bekerjasama',$assessment->aspek_2],
    [3,'Kemandirian',$assessment->aspek_3],
    [4,'Kreativitas',$assessment->aspek_4],
    [5,'Kemampuan menyelesaikan pekerjaan di bidangnya',$assessment->aspek_5],
    [6,'Kemampuan mengidentifikasi dan memecahkan masalah',$assessment->aspek_6],
];

// ===========================
// ✅ FORMAT NIP (8-6-1-3) UNTUK HALAMAN FORM PENILAIAN
// (kalau halaman ini dicetak terpisah, tetap benar)
// ===========================
$nipRaw2 = preg_replace('/\s+/', '', (string)($about->nip_kepala_instansi ?? ''));
$nipFormatted2 = $nipRaw2;

if (strlen($nipRaw2) === 18) {
    $nipFormatted2 =
        substr($nipRaw2, 0, 8) . ' ' .
        substr($nipRaw2, 8, 6) . ' ' .
        substr($nipRaw2, 14, 1) . ' ' .
        substr($nipRaw2, 15, 3);
}
@endphp

<div style="
    position:absolute; left:0; top:0;
    width:330mm; height:210mm;
    padding:5mm 7mm 0mm 18mm;
    font-family:'Arial', sans-serif;
    color:#111;
">

  {{-- JUDUL --}}
  <div style="text-align:center; font-size:28px; font-weight:700; margin:3mm 3mm 0;">
    Form Penilaian Magang Kerja
  </div>

  <br>

  {{-- IDENTITAS --}}
  <table style="width:100%; font-size:18px; line-height:1.12;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="width:32mm; padding:0.5mm 0;">Nama</td>
      <td style="width:8mm;  padding:0.5mm 0; text-align:center;">:</td>
      <td style="padding:0.5mm 0;"><b>{{ $student->name }}</b></td>
    </tr>
    <tr>
      <td style="padding:0.5mm 0;">NIM</td>
      <td style="padding:0.5mm 0; text-align:center;">:</td>
      <td style="padding:0.5mm 0;">{{ $student->nim }}</td>
    </tr>
    <tr>
      <td style="padding:0.5mm 0;">Program Studi</td>
      <td style="padding:0.5mm 0; text-align:center;">:</td>
      <td style="padding:0.5mm 0;">{{ $student->prodi }}</td>
    </tr>
    <tr>
      <td style="padding:0.5mm 0;">Fakultas</td>
      <td style="padding:0.5mm 0; text-align:center;">:</td>
      <td style="padding:0.5mm 0;">{{ $student->fakultas }}, {{ $student->instansi }}</td>
    </tr>
  </table>

  <div style="
    margin-top:2.5mm;
    font-size:17px;
    line-height:1.18;
    width:305mm;
    white-space:normal;
    word-wrap:break-word;
    overflow-wrap:break-word;
    word-break:break-word;
">
    Telah melaksanakan Magang Kerja di Kantor
    {{ $about->nama_instansi ?? '-' }} Kabupaten Karanganyar
    dengan nilai sebagai berikut:
  </div>

  {{-- TABEL NILAI --}}
  <table style="
        width:250mm;
        margin-top:2.5mm;
        margin-left:22mm;
        font-size:17.5px;
        border-collapse:collapse;
        border:1.2px solid #000;
    ">
    <colgroup>
      <col style="width:12mm;">
      <col style="width:188mm;">
      <col style="width:50mm;">
    </colgroup>

    <thead>
      <tr>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">No</th>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">Aspek Penilaian</th>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">Skor</th>
      </tr>
    </thead>

    <tbody>
      @foreach ($rows as [$no,$aspek,$skor])
      <tr>
        <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">{{ $no }}.</td>
        <td style="padding:1.0mm; line-height:1.15; border:1.2px solid #000;">{{ $aspek }}</td>
        <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">{{ $skor }}</td>
      </tr>
      @endforeach

      <tr>
        <th colspan="2" style="padding:1.0mm; text-align:center; border:1.2px solid #000;">TOTAL</th>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">{{ $assessment->total }}</th>
      </tr>
      <tr>
        <th colspan="2" style="padding:1.0mm; text-align:center; border:1.2px solid #000;">RATA-RATA</th>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">
          {{ number_format($assessment->average, 2, ',', '.') }}
        </th>
      </tr>
      <tr>
        <th colspan="2" style="padding:1.0mm; text-align:center; border:1.2px solid #000;">NILAI HURUF</th>
        <th style="padding:1.0mm; text-align:center; border:1.2px solid #000;">{{ $assessment->grade_letter }}</th>
      </tr>
    </tbody>
  </table>

  {{-- BAWAH --}}
  <table style="width:100%; margin-top:5mm; margin-left:7mm;" cellpadding="0" cellspacing="0">
    <tr>
      {{-- KETERANGAN --}}
      <td style="width:118mm; vertical-align:top; padding-right:5mm;">
        <div style="font-size:16.5px; font-weight:700; margin-bottom:1.5mm;">Keterangan:</div>

        <table style="width:100%; font-size:16px; border-collapse:collapse; border:1.5px solid #000;" cellspacing="0" cellpadding="0">
          <tr>
            <th style="padding:1.1mm; text-align:center; border:1.2px solid #000;">Nilai Standar</th>
            <th style="padding:1.1mm; text-align:center; border:1.2px solid #000;">Nilai Huruf</th>
          </tr>
          <tr>
            <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">0 – 30</td>
            <td style="padding:1.0mm; border:1.2px solid #000;">E (sangat kurang)</td>
          </tr>
          <tr>
            <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">31 – 55</td>
            <td style="padding:1.0mm; border:1.2px solid #000;">D (kurang)</td>
          </tr>
          <tr>
            <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">56 – 75</td>
            <td style="padding:1.0mm; border:1.2px solid #000;">C (cukup)</td>
          </tr>
          <tr>
            <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">76 – 90</td>
            <td style="padding:1.0mm; border:1.2px solid #000;">B (baik)</td>
          </tr>
          <tr>
            <td style="padding:1.0mm; text-align:center; border:1.2px solid #000;">91 – 100</td>
            <td style="padding:1.0mm; border:1.2px solid #000;">A (sangat baik)</td>
          </tr>
        </table>
      </td>

      {{-- TTD (DINAMIS DARI ABOUT) --}}
      <td style="vertical-align:top; text-align:center; font-size:16px; line-height:1.2;">

        <div style="margin-top:0; font-weight:700;">Karanganyar, {{ $hariIni }}</div>

        <div style="
          width:150mm;
          margin:0 auto;
          white-space:normal;
          word-wrap:break-word;
          overflow-wrap:break-word;
          word-break:break-word;
        ">
          Kepala {{ $about->nama_instansi ?? '-' }}<br>
          Kabupaten Karanganyar
        </div>

        <div style="height:26mm;"></div>

        <b style="font-size:16px;">{{ $about->kepala_instansi ?? '-' }}</b><br>

        {{-- ✅ NIP DIFORMAT (8-6-1-3) HANYA TAMPILAN --}}
        NIP. {{ $nipFormatted2 ?: '-' }}

      </td>

    </tr>
  </table>

</div>

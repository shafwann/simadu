@php
$hariIni = $today->translatedFormat('d F Y');

$rows = [
[1,'Etika dan Kedisiplinan',$assessment->aspek_1],
[2,'Kemampuan Berkomunikasi dan Bekerjasama',$assessment->aspek_2],
[3,'Kemandirian',$assessment->aspek_3],
[4,'Kreativitas',$assessment->aspek_4],
[5,'Kemampuan menyelesaikan pekerjaan di bidangnya',$assessment->aspek_5],
[6,'Kemampuan mengidentifikasi dan memecahkan masalah',$assessment->aspek_6],
];
@endphp

<div style="
  position:absolute; left:0; top:0;
  width:297mm; height:205mm;
  padding:7mm 7mm 0mm 25mm;              /* lebih pas kiri kanan */
  font-family:'DejaVu Sans', sans-serif;
  color:#111;
">
{{-- JUDUL --}}
    <div style="text-align:center; top: 198px; font-size:24px; font-weight:700; margin:3mm 3mm 0;">
        Form Penilaian Magang Kerja
    </div>
    {{-- IDENTITAS --}}
    <br>
  <table style="width:100%; font-size:15px; line-height:1.12;" cellpadding="0" cellspacing="0">
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

  <div style="margin-top:2.5mm; font-size:13px; line-height:1.18;">
    Telah melaksanakan Magang Kerja di Kantor Badan Perencanaan Pembangunan,
    Riset, dan Inovasi Daerah Kabupaten Karanganyar dengan nilai sebagai berikut:
  </div>

  {{-- TABEL NILAI (lebar tabel dibuat sedikit lebih lebar + skor dibesarkan) --}}
  <table style="
      width:250mm;                 /* lebih lebar biar kanan-kiri enak */
      margin-top:2.5mm;
      margin-left:15mm;
      font-size:13.5px;
      border-collapse:collapse;
      border:1.2px solid #000;     /* tebalin garis luar */
  ">

    <colgroup>
      <col style="width:12mm;">    {{-- No --}}
      <col style="width:188mm;">   {{-- Aspek --}}
      <col style="width:50mm;">    {{-- Skor (DILEBARKAN) --}}
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
          {{ number_format($assessment->average,2,',','.') }}
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
      <div style="font-size:13.5px; font-weight:700; margin-bottom:1.5mm;">Keterangan:</div>

      <table style="
  width:100%;
  font-size:13px;
  border-collapse:collapse;
  border:1.5px solid #000;
" cellspacing="0" cellpadding="0">
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

    {{-- TTD (SEJAJAR DENGAN KETERANGAN) --}}
    <td style="vertical-align:top; text-align:center; font-size:13px; line-height:1.2;">
      <div style="margin-top:0; font-weight: 700;">Karanganyar, {{ $hariIni }}</div>
      <div style="margin-top:0;">
        Kepala Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah<br>
        Kabupaten Karanganyar
      </div>

      {{-- ruang tanda tangan --}}
      <div style="height:28mm;"></div>

      <b style="font-size:13px;">Dwi Cahyono, S.Sos., M.Si.</b><br>
      NIP. 19730614 199303 1 002
    </td>
  </tr>
</table>


</div>
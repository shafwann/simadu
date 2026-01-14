<!doctype html>
<html>
<head>
<meta charset="utf-8">

<style>
@page { margin: 14px 18px; }

body {
    font-family: arialcustom, Arial, sans-serif;
    font-size: 12px;
    color: #111;
}

/* ===== TITLE ===== */
.title { text-align: center; margin-top: 40px; }
.title .h1, .title .h2, .title .h3 {
    font-size: 19px;
    margin: 0;
    line-height: 1.15;
}
.title .h2 { margin-top: 3px; }
.title .h3 { margin-top: 3px; }
.title .h4 { font-size: 18px; margin: 3px 0 10px; }

/* ===== INFO ===== */
.info {
    width: 100%;
    margin-top: 8px;
    font-size: 12px;
}
.info td { padding: 2px; vertical-align: top; }
.label { width: 155px; font-weight: 700; }
.colon { width: 12px; }

/* ===== TABEL LOGBOOK ===== */
table.logbook {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
    font-size: 12px;
}
table.logbook th,
table.logbook td {
    border: 1px solid #000;
    padding: 6px;
    vertical-align: top;
}
table.logbook th {
    text-align: center;
    font-weight: 800;
}
tr { page-break-inside: auto; }
td { page-break-inside: auto; word-break: break-word; }

/* ===== FOOT ===== */
table.foot { width: 100%; margin-top: 14px; font-size: 12px; }
table.foot td { vertical-align: top; }
.sign .gap { height: 46px; }
.sign .name { font-size: 12px; }
</style>
</head>

<body>

{{-- ===== JUDUL ===== --}}
<div class="title">
    <b>
        <div class="h1">LOGBOOK KEGIATAN MAGANG</div>
        <div class="h2">{{ strtoupper($about->nama_instansi ?? 'NAMA INSTANSI') }}</div>
        <div class="h3">KABUPATEN KARANGANYAR</div>
        <div class="h4">TAHUN {{ $year }}</div>
    </b>
</div>

{{-- ===== DATA DIRI ===== --}}
<table class="info" style="margin-left:20px;">
<tr>
    <td class="label">Nama</td><td class="colon">:</td>
    <td><b>{{ $student->name }}</b></td>
</tr>
<tr>
    <td class="label">NIM</td><td class="colon">:</td>
    <td>{{ $student->nim }}</td>
</tr>
<tr>
    <td class="label">Program Studi</td><td class="colon">:</td>
    <td>{{ $student->prodi }}</td>
</tr>
<tr>
    <td class="label">Fakultas</td><td class="colon">:</td>
    <td>{{ $student->fakultas }}</td>
</tr>
<tr>
    <td class="label">Universitas</td><td class="colon">:</td>
    <td>{{ $student->instansi }}</td>
</tr>
<tr class="bulanRow">
    <td class="label">Bulan</td>
    <td class="colon">:</td>
    <td class="bulanValue">{{ strtoupper($bulanLabel) }}</td>
</tr>
</table>

{{-- ===== LOGBOOK ===== --}}
<table class="logbook">
<thead>
<tr>
    <th width="5%">No</th>
    <th width="35%">Hari, Tanggal</th>
    <th width="12%">Waktu</th>
    <th>Kegiatan</th>
    <th width="16%">Validasi</th>
</tr>
</thead>
<tbody>
@php $no = 1; @endphp
@forelse($logbooks as $log)
<tr>
    <td align="center">{{ $no++ }}</td>
    <td align="center">
        {{ \Carbon\Carbon::parse($log->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
    </td>
    <td align="center">{{ $log->created_at?->format('H:i') ?? '-' }}</td>
    <td style="word-wrap: break-word; white-space: pre-line;">{!! nl2br(e($log->kegiatan)) !!}</td>
    <td align="center">{{ $log->is_validated ? 'Disetujui' : 'Belum Disetujui' }}</td>
</tr>
@empty
<tr>
    <td colspan="5" align="center">Tidak ada data logbook.</td>
</tr>
@endforelse
</tbody>
</table>

{{-- ===== FOOTER TANDA TANGAN ===== --}}
<table class="foot">
<tr>
    <td width="55%"></td>
    <td class="sign">
        <div>a.n. Kepala {{ $about->nama_instansi ?? 'Instansi' }}</div>
        <div>Kabupaten Karanganyar</div>
        <div>{{ $student->jabatan_pembimbing ?? '-' }}</div>

        <div class="gap"></div><br>

        <div class="name"><b>{{ $student->nama_pembimbing ?? '-' }}</b></div>
        <div>NIP. {{ $student->nip_pembimbing ?? '-' }}</div>
    </td>
</tr>
</table>

</body>
</html>

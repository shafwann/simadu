<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* ✅ margin rapat biar full 1 halaman */
        @page {
            margin: 14px 18px;
        }

        /* ✅ paksa font ArialCustom untuk semua teks */
        body {
            font-family: arialcustom, Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        /* ===== HEADER: h1/h2/h3 sama besar & besar seperti contoh ===== */
        .title {
            text-align: center;
            margin-top: 40px;
        }

        .title .h1,
        .title .h2,
        .title .h3 {
            font-size: 19px;
            margin: 0;
            line-height: 1.15;
        }

        .title .h2 {
            margin-top: 3px;
        }

        .title .h3 {
            margin-top: 3px;
        }

        .title .h4 {
            font-size: 18px;
            margin: 3px 0 10px;
        }

        /* ===== IDENTITAS dibesarkan ===== */
        .info {
            width: 100%;
            margin-top: 8px;
            font-size: 18px;
        }

        .info td {
            padding: 2px 2px;
            vertical-align: top;
        }

        .label {
            width: 155px;
        }

        .colon {
            width: 12px;
        }

        .bulanRow td {
            padding-top: 6px;
        }

        .bulanValue {
            font-weight: 700;
        }

        /* ===== GRID 2 TABLE ===== */
        .grid {
            width: 100%;
            margin-top: 16px;
        }

        .col {
            width: 49%;
            vertical-align: top;
        }

        .spacer {
            width: 2%;
        }

        /* ===== FOOTER: Keterangan kiri + TTD kanan (dibesarkan) ===== */
        table.foot {
            width: 100%;
            margin-top: 14px;
        }

        table.foot td {
            vertical-align: top;
        }

        .note {
            width: 52%;
            font-size: 16px;
        }

        .note .head {
            margin-bottom: 4px;
            font-size: 16px;
        }

        .sign {
            width: 48%;
            font-size: 16px;
        }

        .sign .gap {
            height: 46px;
        }

        .sign .name {
            font-size: 16px;
        }
    </style>
</head>

<body>

    <div class="title" style="margin-top: 40px;">
        <b>
            <div class="h1">DAFTAR HADIR MAGANG</div>
            <div class="h2">{{ strtoupper($about->nama_instansi ?? 'NAMA INSTANSI') }}</div>
            <div class="h3">KABUPATEN KARANGANYAR</div>
            <div class="h4">TAHUN {{ $year }}</div>
        </b>
    </div>

    <table class="info" style="margin-left: 20px;">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td><b>{{ $student->name }}</b></td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td class="colon">:</td>
            <td>{{ $student->nim }}</td>
        </tr>
        <tr>
            <td class="label">Program Studi</td>
            <td class="colon">:</td>
            <td>{{ $student->prodi }}</td>
        </tr>
        <tr>
            <td class="label">Fakultas</td>
            <td class="colon">:</td>
            <td>{{ $student->fakultas }}</td>
        </tr>
        <tr>
            <td class="label">Universitas</td>
            <td class="colon">:</td>
            <td>{{ $student->instansi }}</td>
        </tr>
        <tr class="bulanRow">
            <td class="label">Bulan</td>
            <td class="colon">:</td>
            <td class="bulanValue">{{ strtoupper($bulanLabel) }}</td>
        </tr>
    </table>

    <table class="grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col">@include('admin.students._presensi_table', ['rows' => $left])</td>
            <td class="spacer"></td>
            <td class="col">@include('admin.students._presensi_table', ['rows' => $right])</td>
        </tr>
    </table>

    <table class="foot" cellpadding="0" cellspacing="0">
        <tr>
            <td class="note">
                <div class="head"><b>Keterangan :</b></div>
                <div>I = Ijin</div>
                <div>S = Sakit</div>
                <div>DL = Dinas Luar</div>
                <div>CT = Cuti</div>
                <div>TK = Tanpa Keterangan</div>
            </td>

            <td class="sign">
                <div>a.n. Kepala {{ $about->nama_instansi ?? 'Instansi' }}</div>
                <div>Kabupaten Karanganyar</div>
                <div>{{ $student->jabatan_pembimbing ?? '-' }}</div>

                <div class="gap"></div>

                <div class="name">{{ $student->nama_pembimbing ?? '-' }}</div>
                <div>NIP. {{ $student->nip_pembimbing ?? '-' }}</div>

            </td>
        </tr>
    </table>

</body>

</html>
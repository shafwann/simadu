@php
use Carbon\Carbon;

$mulai = Carbon::parse($student->tanggal_masuk)->translatedFormat('d F Y');
$selesai = Carbon::parse($student->tanggal_selesai)->translatedFormat('d F Y');
$hariIni = $today->translatedFormat('d F Y');

// ✅ TAMBAHAN BARU: jika ada tanggal dari form (certificate_date), pakai itu untuk sertifikat
if (!empty($assessment->certificate_date)) {
    $hariIni = Carbon::parse($assessment->certificate_date)->translatedFormat('d F Y');
}

// ✅ TAMBAHAN BARU: jika certificate_date sudah berupa Carbon (casts), pakai langsung
if (!empty($assessment->certificate_date) && method_exists($assessment->certificate_date, 'translatedFormat')) {
    $hariIni = $assessment->certificate_date->translatedFormat('d F Y');
}

/* ==== INSTANSI: paksa 1 baris + auto kecilkan font ==== */
$instansiText = trim((string) ($student->instansi ?? ''));
$len = mb_strlen($instansiText, 'UTF-8');

if ($len <= 20) {
    $instansiFont=24;
    } elseif ($len <=28) {
    $instansiFont=22;
    } elseif ($len <=36) {
    $instansiFont=20.5;
    } elseif ($len <=44) {
    $instansiFont=19.5;
    } else {
    $instansiFont=18.5;
    }

    /* Style string aman (1 line) */
    $instansiStyle='display:block;margin-top:2px;font-weight:700;line-height:1.15;white-space:nowrap;font-size:' .$instansiFont.'px;';
    @endphp

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">

        <style>
            /* === F4 LANDSCAPE === */
            @page {
                size: 330mm 210mm;
                margin: 0;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                width: 330mm;
                height: 210mm;
            }

            /* Wrapper 1 halaman */
            .sertifikat {
                position: relative;
                width: 330mm;
                height: 210mm;
                overflow: hidden;
                /* PENTING: cegah halaman ke-2 */
                font-family: 'Arial', sans-serif;
                color: #111;

                page-break-after: avoid;
                page-break-before: avoid;
                page-break-inside: avoid;
            }

            /* cegah DomPDF memotong elemen */
            * {
                box-sizing: border-box;
                page-break-inside: avoid;
            }
        </style>
    </head>

    <body>
        <div class="sertifikat">

            {{-- MAGANG KERJA --}}
<div style="position:absolute; top:50mm; left:80mm;">

    {{-- ✅ TAMBAHAN BARU: judul dinamis dari input Jenis Kegiatan --}}
    <div style="font-size:40px; font-weight:700;">
        {{ $student->kegiatan ?? 'Magang Kerja' }}
    </div>
</div>


            {{-- NOMOR --}}
            <div style="position:absolute; top:64mm; left:80mm; font-size:21px;">
                Nomor : {{ $assessment->certificate_no }}
            </div>

            {{-- DIBERIKAN KEPADA --}}
            <div style="position:absolute; top:72mm; left:95mm; width:140mm; text-align:center; font-size:20px; font-weight:700;">
                Diberikan Kepada :
            </div>

            {{-- IDENTITAS --}}
            <div style="position:absolute; top:80mm; left:100mm; width:140mm; font-size:20px; line-height:1.35;">
                <table cellpadding="0" cellspacing="0" style="width:100%;">
                    <tr>
                        <td style="width:45mm; vertical-align:top; padding-top:6px; line-height:1.35;">
                            <span style="position:relative; top:-6px;">Nama</span><br>
                            NIM<br>
                            Program Studi<br>
                            Fakultas
                        </td>

                        <td style="width:6mm; vertical-align:top; text-align:center; padding-top:6px; line-height:1.35;">
                            <span style="position:relative; top:-6px;">:</span><br>
                            :<br>
                            :<br>
                            :
                        </td>

                        <td style="vertical-align:top;">
                            <span style="font-weight:700; font-size:24px; padding-top:1px;">{{ $student->name }}</span><br>
                            {{ $student->nim }}<br>
                            {{ $student->prodi }}<br>
                            {{ $student->fakultas }}<br>

                            {{-- instansi mahasiswa (1 baris + auto font) --}}
                            <span style="<?php echo $instansiStyle; ?>"> <?php echo e($instansiText); ?> </span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- PARAGRAF (DINAMIS NAMA INSTANSI DARI ABOUT) --}}
            <div style="position:absolute; top:123mm; left:40mm; right:40mm; text-align:center; font-size:18px; line-height:1.30;">
    Telah melaksanakan {{ $student->kegiatan ?? 'Magang Kerja' }} di
    {{ $about->nama_instansi ?? '-' }} Kabupaten Karanganyar
    dari {{ $mulai }} sampai {{ $selesai }} dengan hasil
    <span style="font-weight:700; text-transform:uppercase;">{{ $hasilLabel }}</span>.
</div>


            {{-- TANGGAL --}}
            <div style="position:absolute; top:139mm; left:60mm; width:210mm; text-align:center; font-size: 18.5px; font-weight:700;">
                Karanganyar, {{ $hariIni }}
            </div>

            {{-- JABATAN + TTD (DINAMIS NAMA + NIP DARI ABOUT) --}}
            <div style="position:absolute; top:145mm; left:60mm; width:210mm; text-align:center; font-size:18px; line-height:1.10;">
                <div style="margin-top:0;">
                    Kepala {{ $about->nama_instansi ?? '-' }}
                </div>
                <div style="margin-top:0;">
                    Kabupaten Karanganyar
                </div>
                <div style="height:26mm;"></div>

                <div style="font-weight:700; line-height:1.30;">
                    {{ $about->kepala_instansi ?? '-' }}
                </div>
                <div style="font-weight:700; line-height:1.30;">
                    NIP. {{ $about->nip_kepala_instansi ?? '-' }}
                </div>
            </div>



        </div>
    </body>

    </html>

@php
use Carbon\Carbon;

$judulRaw = trim((string) ($student->kegiatan ?? 'Magang Kerja'));
$judulKegiatan = ucwords(mb_strtolower($judulRaw, 'UTF-8'));

// ✅ TAMBAHAN BARU: jika certificate_date sudah berupa Carbon (casts), pakai langsung
if (!empty($assessment->certificate_date) && method_exists($assessment->certificate_date, 'translatedFormat')) {
    $hariIni = $assessment->certificate_date->translatedFormat('d F Y');
}

/* ✅ AMBIL APRESIASI KHUSUS DARI DATABASE (BARU) */
$apresiasiKhusus = trim((string)($assessment->apresiasi_khusus ?? ''));

/* ✅ FIX ERROR: variabel yang dipakai di bawah harus ada */
$tanggalSertifikat = $hariIni;

/* ==== FORMAT NIP (tetap sama) ==== */
$nipRaw = preg_replace('/\s+/', '', (string)($about->nip_kepala_instansi ?? ''));
$nipSertifikat = $nipRaw;

if (strlen($nipRaw) === 18) {
    $nipSertifikat =
        substr($nipRaw, 0, 8) . ' ' .
        substr($nipRaw, 8, 6) . ' ' .
        substr($nipRaw, 14, 1) . ' ' .
        substr($nipRaw, 15, 3);
}
@endphp

{{-- MAGANG KERJA --}}
<div style="position:absolute; top:50mm; left:80mm;">
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
                <span style="{!! $instansiStyle !!}">{{ $instansiText }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- PARAGRAF (DINAMIS NAMA INSTANSI DARI ABOUT) --}}
<div style="
    position:absolute;
    top:123mm;
    left:40mm;
    right:40mm;
    text-align:center;
    font-size:18px;
    line-height:1.35;
    max-height:30mm;
    overflow:hidden;
">
    Telah melaksanakan {{ strtolower($judulKegiatan) }} di {{ $about->nama_instansi ?? '-' }} Kabupaten Karanganyar dari {{ $mulai }} sampai {{ $selesai }}
    @if($apresiasiKhusus !== '')
        <br>
        <span style="font-style:italic;">
            {{ $apresiasiKhusus }}
        </span>
    @endif
    dengan hasil
    <span style="font-weight:700; text-transform:uppercase;">{{ $hasilLabel }}</span>.
</div>

{{-- TANGGAL --}}
<div style="position:absolute; top:139mm; left:60mm; width:210mm; text-align:center; font-size: 18.5px; font-weight:700;">
    Karanganyar, {{ $tanggalSertifikat }}
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
        NIP. {{ $nipSertifikat ?: '-' }}
    </div>
</div>

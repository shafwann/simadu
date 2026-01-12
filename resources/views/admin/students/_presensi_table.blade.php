<style>
    table.presensi-simple {
        width: 100%;
        border-collapse: collapse;
        /* 🔴 garis nyatu & jelas */
        table-layout: fixed;
        font-family: arialcustom, Arial, sans-serif;
        font-size: 11px;
        color: #111;
    }

    /* ================= HEADER ================= */
    table.presensi-simple thead th {
        text-align: center;
        font-weight: 800;
        /* 🔴 lebih tebal */
        color: #000;
        /* 🔴 hitam pekat */
        padding: 8px 6px;
        border: 1px solid #000;
        /* 🔴 garis header tebal */
        background: #fff;
        white-space: nowrap;
    }

    /* ================= BODY ================= */
    table.presensi-simple tbody td {
        padding: 7px 6px;
        border: 1px solid #000;
        /* 🔴 garis isi tabel ditebalkan */
        vertical-align: middle;
        text-align: center;
        line-height: 1.1;
    }

    /* Lebar kolom (dipertahankan agar 1 halaman) */
    .col-tanggal {
        width: 28%;
        font-weight: 700;
    }

    .col-in {
        width: 14%;
    }

    .col-out {
        width: 14%;
    }

    .col-status {
        width: 16%;
    }

    .col-lokasi {
        width: 28%;
    }

    /* 🔴 baris libur */
    .holiday-row td {
        background: #d6a8a8;
    }

    /* ================= BADGE ================= */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 11px;
    }

    .badge-hadir {
        background: #dcfce7;
        color: #166534;
    }

    .badge-telat {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-ijin {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-sakit {
        background: #fde68a;
        color: #92400e;
    }

    .badge-dl {
        background: #e9d5ff;
        color: #6b21a8;
    }

    .badge-cuti {
        background: #fbcfe8;
        color: #9d174d;
    }

    .badge-tk {
        background: #e5e7eb;
        color: #374151;
    }

    .loc-empty {
        color: #777;
        font-style: italic;
    }
</style>

<table class="presensi-simple">
    <thead>
        <tr>
            <th class="col-tanggal">TANGGAL</th>
            <th class="col-in">CHECK
                <br>IN
            </th>
            <th class="col-out">CHECK
                <br>OUT
            </th>
            <th class="col-status">STATUS</th>
            <th class="col-lokasi">LOKASI</th> {{-- 🔴 MAP DIHILANGKAN --}}
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $r)
        <tr class="{{ $r['isHoliday'] ? 'holiday-row' : '' }}">
            <td class="col-tanggal">{{ $r['tanggal_label'] }}</td>
            <td class="col-in">{{ $r['checkin_label'] }}</td>
            <td class="col-out">{{ $r['checkout_label'] }}</td>

            <td class="col-status">
                @php
                $raw = strtoupper(trim($r['status'] ?? '')); // status dari DB (bisa kosong)

                // ✅ kalau kosong: tetap "-"
                if ($raw === '' || $raw === '-') {
                $badge = 'badge-tk';
                $label = '-';
                }
                // ✅ TK hanya kalau Alpha / Tanpa Keterangan / TK
                elseif (in_array($raw, ['ALPHA', 'TANPA KETERANGAN', 'TK'])) {
                $badge = 'badge-tk';
                $label = 'TK';
                }
                elseif ($raw === 'HADIR') {
                $badge = 'badge-hadir';
                $label = 'Hadir';
                }
                elseif ($raw === 'TELAT') {
                $badge = 'badge-telat';
                $label = 'Telat';
                }
                elseif (in_array($raw, ['I', 'IZIN', 'IJIN'])) {
                $badge = 'badge-ijin';
                $label = 'I';
                }
                elseif (in_array($raw, ['S', 'SAKIT'])) {
                $badge = 'badge-sakit';
                $label = 'S';
                }
                elseif ($raw === 'DL' || $raw === 'DINAS LUAR') {
                $badge = 'badge-dl';
                $label = 'DL';
                }
                elseif ($raw === 'CT' || $raw === 'CUTI') {
                $badge = 'badge-cuti';
                $label = 'CT';
                }
                @endphp

                <span class="badge {{ $badge }}">{{ $label }}</span>
            </td>

            <td class="col-lokasi">
                @php
                $locRaw = trim($r['location'] ?? '');
                $loc = $locRaw;

                // ambil src dari iframe
                if ($loc && stripos($loc, '<iframe') !==false) {
                    preg_match('/src="([^" ]+)"/i', $loc, $m);
                    $loc=$m[1] ?? '' ;
                    }

                    // normalisasi
                    $loc=trim($loc);

                    // kalau masih kosong → kosong
                    if ($loc==='' ) {
                    $loc='' ;
                    }
                    // kalau ga ada http → tambahkan https
                    elseif (!preg_match('/^https?:\/\//i', $loc)) {
                    $loc='https://' . $loc;
                    }
                    @endphp

                    @if(!empty($loc))
                    <a href="{{ $loc }}" style="
        display:inline-block;
        max-width:100%;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
        text-decoration:none;
        color:#111;
        font-weight:700;
    ">BAPPERIDA</a>
                    @else
                    <span class="loc-empty">Lokasi Kosong</span>
                    @endif
            </td>

        </tr>
        @endforeach
    </tbody>
</table>

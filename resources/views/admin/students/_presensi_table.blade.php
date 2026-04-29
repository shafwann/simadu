<style>
    table.presensi-simple {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-family: arialcustom, Arial, sans-serif;
        font-size: 11px;
        color: #111;
    }

    table.presensi-simple thead th {
        text-align: center;
        font-weight: 800;
        color: #000;
        padding: 8px 6px;
        border: 1px solid #000;
        background: #fff;
        white-space: nowrap;
    }

    table.presensi-simple tbody td {
        padding: 7px 6px;
        border: 1px solid #000;
        vertical-align: middle;
        text-align: center;
        line-height: 1.1;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

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

    /* merah libur */
    .holiday-row td {
        background: #d6a8a8;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 11px;
        margin: 2px 2px;
        line-height: 1;
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

    .badge-pc {
        background: #ffedd5;
        color: #9a3412;
    }

    .badge-tap {
        background: #fee2e2;
        color: #991b1b;
    }

    .holiday-name {
        display: block;
        margin-top: 3px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .lokasi-text {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        color: #000;
        display: block;
    }

    /* DomPDF kadang kurang suka link style kompleks; ini aman */
    a.lokasi-link {
        color: #000;
        text-decoration: none;
    }
</style>

<table class="presensi-simple">
    <thead>
        <tr>
            <th class="col-tanggal">TANGGAL</th>
            <th class="col-in">JAM<br>MASUK</th>
            <th class="col-out">JAM<br>PULANG</th>
            <th class="col-status">STATUS</th>
            <th class="col-lokasi">LOKASI</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($rows as $r)
            <tr class="{{ !empty($r['isHoliday']) ? 'holiday-row' : '' }}">
                <td class="col-tanggal">
                    {{ $r['tanggal_label'] }}

                    {{-- OPTIONAL: tampilkan nama hari libur dari API/manual --}}
                    @if (!empty($r['holiday_name']))
                        <span class="holiday-name">{{ $r['holiday_name'] }}</span>
                    @endif
                </td>

                <td class="col-in">{{ $r['checkin_label'] }}</td>
                <td class="col-out">{{ $r['checkout_label'] }}</td>

                <td class="col-status">
                    @php
                        $raw = strtoupper(trim($r['status'] ?? ''));
                        $parts = array_values(array_filter(array_map('trim', explode(',', $raw)), fn($v) => $v !== ''));

                        $main = $parts[0] ?? '-';
                        $extras = $parts;
                        if (!empty($extras)) {
                            array_shift($extras);
                        }

                        $badge = 'badge-tk';
                        $label = $main;

                        switch ($main) {
                            case 'H':
                                $badge = 'badge-hadir';
                                $label = 'H';
                                break;
                            case 'T':
                                $badge = 'badge-telat';
                                $label = 'T';
                                break;
                            case 'I':
                                $badge = 'badge-ijin';
                                $label = 'I';
                                break;
                            case 'S':
                                $badge = 'badge-sakit';
                                $label = 'S';
                                break;
                            case 'DL':
                                $badge = 'badge-dl';
                                $label = 'DL';
                                break;
                            case 'CT':
                                $badge = 'badge-cuti';
                                $label = 'CT';
                                break;
                            case 'TK':
                                $badge = 'badge-tk';
                                $label = 'TK';
                                break;
                            case '-':
                            default:
                                $badge = 'badge-tk';
                                $label = $main === '' ? '-' : $main;
                                break;
                        }
                    @endphp

                    <span class="badge {{ $badge }}">{{ $label }}</span>
                </td>

                <td class="col-lokasi">
                    @if (!empty($r['location_url']))
                        <a class="lokasi-link" href="{{ $r['location_url'] }}">
                            <span class="lokasi-text">{{ $r['location_text'] }}</span>
                        </a>
                    @else
                        <span class="lokasi-text">{{ $r['location_text'] }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

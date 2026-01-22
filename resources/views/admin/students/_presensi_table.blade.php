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
    }

    .col-tanggal { width: 28%; font-weight: 700; }
    .col-in { width: 14%; }
    .col-out { width: 14%; }
    .col-status { width: 16%; }
    .col-lokasi { width: 28%; }

    .holiday-row td { background: #d6a8a8; }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 11px;
        margin: 2px 2px;
    }

    .badge-hadir { background: #dcfce7; color: #166534; }
    .badge-telat { background: #fef9c3; color: #854d0e; }
    .badge-ijin  { background: #dbeafe; color: #1e40af; }
    .badge-sakit { background: #fde68a; color: #92400e; }
    .badge-dl    { background: #e9d5ff; color: #6b21a8; }
    .badge-cuti  { background: #fbcfe8; color: #9d174d; }
    .badge-tk    { background: #e5e7eb; color: #374151; }
    .badge-pc    { background: #ffedd5; color: #9a3412; }
    .badge-tap   { background: #fee2e2; color: #991b1b; }
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
        @foreach($rows as $r)
            <tr class="{{ $r['isHoliday'] ? 'holiday-row' : '' }}">
                <td class="col-tanggal">{{ $r['tanggal_label'] }}</td>
                <td class="col-in">{{ $r['checkin_label'] }}</td>
                <td class="col-out">{{ $r['checkout_label'] }}</td>

                <td class="col-status">
                    @php
                        $raw = strtoupper(trim($r['status'] ?? ''));
                        $parts = array_filter(array_map('trim', explode(',', $raw)));

                        $main = $parts[0] ?? '-';
                        $extras = $parts;
                        if (!empty($extras)) array_shift($extras);

                        $badge = 'badge-tk';
                        $label = $main;

                        if ($main === '' || $main === '-') {
                            $badge = 'badge-tk'; $label = '-';
                        } elseif ($main === 'TK') {
                            $badge = 'badge-tk'; $label = 'TK';
                        } elseif ($main === 'H') {
                            $badge = 'badge-hadir'; $label = 'H';
                        } elseif ($main === 'T') {
                            $badge = 'badge-telat'; $label = 'T';
                        } elseif ($main === 'I') {
                            $badge = 'badge-ijin'; $label = 'I';
                        } elseif ($main === 'S') {
                            $badge = 'badge-sakit'; $label = 'S';
                        } elseif ($main === 'DL') {
                            $badge = 'badge-dl'; $label = 'DL';
                        } elseif ($main === 'CT') {
                            $badge = 'badge-cuti'; $label = 'CT';
                        }

                        $showPC  = in_array('PC', $extras);
                        $showTAP = in_array('TAP', $extras);
                    @endphp

                    <span class="badge {{ $badge }}">{{ $label }}</span>
                    @if($showPC)  <span class="badge badge-pc">PC</span> @endif
                    @if($showTAP) <span class="badge badge-tap">TAP</span> @endif
                </td>

                <td class="col-lokasi">
                    @if(!empty($r['location_url']))
                        <a href="{{ $r['location_url'] }}" target="_blank" style="color:#000;display:block;width:100%;text-decoration:none;">
                            <span style="font-weight:700;text-transform:uppercase;font-size:10px;">
                                {{ $r['location_text'] }}
                            </span>
                        </a>
                    @else
                        <span style="font-weight:700;text-transform:uppercase;font-size:10px;color:#000;">
                            {{ $r['location_text'] }}
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

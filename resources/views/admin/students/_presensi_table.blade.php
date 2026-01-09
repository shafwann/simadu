<style>
    table.presensi {
        width:100%;
        border-collapse:collapse;
        table-layout:fixed;
        font-family: arialcustom, Arial, sans-serif;
    }

    table.presensi th, table.presensi td {
        border:1px solid #111;
        padding:6px 3px;
        font-size:12px;
        line-height:1.05;
    }

    table.presensi th {
        text-align:center;
        font-weight:700;
        font-size:12px;
        padding:8px 2px;
        white-space:nowrap; /* ✅ cegah "TANGGA L" turun */
    }

    /* ✅ Lebar tanggal dilebarkan sedikit */
    .tanggal { width:20%; text-align:center; font-weight:700; }
    .ttd    { width:26%; text-align:center; }
    .kcol   { width:10.8%; text-align:center; font-weight:700; }

    /* ✅ warna libur */
    .holiday { background:#d79a9a; }

    /* ✅ checklist aman: pakai dejavu hanya untuk glyph ✓ */
    .tick {
        display:inline-block;
        width: 12px;
        height: 5px;
        border-left: 2px solid #111;
        border-bottom: 2px solid #111;
        transform: rotate(-45deg);
        margin-top: 2px;
    }

    /* supaya di tengah sel */
    .center { text-align:center; }
</style>

<table class="presensi">
    <thead>
        <tr>
            <th class="tanggal" rowspan="2">TANGGAL</th>
            <th class="ttd" rowspan="2">TANDA<br>TANGAN<br>MAHASISWA</th>
            <th colspan="5">KETERANGAN</th>
        </tr>
        <tr>
            <th class="kcol">I</th>
            <th class="kcol">S</th>
            <th class="kcol">DL</th>
            <th class="kcol">CT</th>
            <th class="kcol">TK</th>
        </tr>
    </thead>

    <tbody>
        @foreach($rows as $r)
            <tr class="{{ $r['isHoliday'] ? 'holiday' : '' }}">
                <td class="tanggal">{{ $r['day'] }}</td>

                <td class="ttd center">
                    @if($r['signed'])
                        <span class="tick"></span>
                    @else
                        &nbsp;
                    @endif
                </td>

                <td class="kcol center">@if($r['mark']['I'])  <span class="tick"></span> @else &nbsp; @endif</td>
                <td class="kcol center">@if($r['mark']['S'])  <span class="tick"></span> @else &nbsp; @endif</td>
                <td class="kcol center">@if($r['mark']['DL']) <span class="tick"></span> @else &nbsp; @endif</td>
                <td class="kcol center">@if($r['mark']['CT']) <span class="tick"></span> @else &nbsp; @endif</td>
                <td class="kcol center">@if($r['mark']['TK']) <span class="tick"></span> @else &nbsp; @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>

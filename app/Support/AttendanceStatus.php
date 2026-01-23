<?php

namespace App\Support;

use Carbon\Carbon;

class AttendanceStatus
{
    public static function mainCode(?string $status): string
    {
        $st = strtoupper(trim((string) $status));

        if ($st === '' || $st === '-') return '-';
        if (in_array($st, ['ALPHA', 'TANPA KETERANGAN', 'TK'], true)) return 'TK';

        if ($st === 'HADIR') return 'H';
        if ($st === 'TELAT') return 'T';

        if (in_array($st, ['IJIN', 'IZIN', 'I'], true)) return 'I';
        if (in_array($st, ['SAKIT', 'S'], true)) return 'S';

        if (in_array($st, ['DINAS LUAR', 'DL'], true)) return 'DL';
        if (in_array($st, ['CUTI', 'CT'], true)) return 'CT';

        return $st; // fallback
    }

    public static function isPC(?string $keterangan): bool
    {
        return strtoupper(trim((string) $keterangan)) === 'PULANG CEPAT';
    }

    public static function isTAP(
        ?string $checkIn,
        ?string $checkOut,
        ?string $checkoutEndStr,
        ?string $tanggalYmd,
        string $tz = 'Asia/Jakarta'
    ): bool {
        if (empty($checkIn)) return false;
        if (!empty($checkOut)) return false;
        if (empty($checkoutEndStr) || empty($tanggalYmd)) return false;

        try {
            $end = Carbon::parse($tanggalYmd . ' ' . $checkoutEndStr, $tz);
            return Carbon::now($tz)->gt($end);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function buildFinal(string $main, bool $pc, bool $tap): string
    {
        $extra = [];
        if ($pc) $extra[] = 'PC';
        if ($tap) $extra[] = 'TAP';

        return empty($extra) ? $main : ($main . ',' . implode(',', $extra));
    }
}

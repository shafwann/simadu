<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Mengambil data dan memetakan kolomnya
        return Attendance::with('user')->get()->map(function($item) {
            return [
                'Nama'    => $item->user->name ?? 'N/A',
                'Tanggal' => $item->tanggal,
                'Jam'     => $item->check_in,
                'Status'  => $item->status,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama Mahasiswa', 'Tanggal', 'Jam Masuk', 'Status'];
    }
}

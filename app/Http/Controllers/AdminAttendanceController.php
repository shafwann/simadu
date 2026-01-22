<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    private function getDefaultLocationName(string $status): string
    {
        if ($status === 'Hadir' || $status === 'Telat') return 'KANTOR BAPPERIDA';
        if ($status === 'Sakit') return 'SAKIT';
        if ($status === 'Ijin') return 'IJIN';
        if ($status === 'Cuti') return 'CUTI';
        if ($status === 'Dinas Luar') return 'DINAS LUAR';
        if ($status === 'Tanpa Keterangan') return 'TANPA KETERANGAN';
        return 'LUAR KANTOR';
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'tanggal'       => 'required|date',
            'jam'           => 'nullable',
            'status'        => 'required|string',
            'tipe_absensi'  => 'nullable|in:check_in,check_out',
            'location'      => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string|max:50', // ✅ untuk PC
        ]);

        $status = $validated['status'];
        if ($status === 'Alpha') $status = 'Tanpa Keterangan';

        $lokasiNama = $validated['location_name'] ?? $this->getDefaultLocationName($status);

        // ===== TIDAK HADIR =====
        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'], true)) {

            $exists = Attendance::where('user_id', $validated['user_id'])
                ->whereDate('tanggal', $validated['tanggal'])
                ->exists();

            if ($exists) {
                return back()->with('error', 'Data untuk tanggal ini sudah ada. Hapus dulu atau Edit.');
            }

            Attendance::create([
                'user_id'       => $validated['user_id'],
                'tanggal'       => $validated['tanggal'],
                'check_in'      => null,
                'check_out'     => null,
                'status'        => $status,
                'keterangan'    => $validated['keterangan'] ?? null,
                'location'      => null,
                'location_name' => $lokasiNama,
            ]);

            return back()->with('success', 'Berhasil input keterangan: ' . $status);
        }

        // ===== HADIR / TELAT =====
        if (!isset($validated['tipe_absensi'])) {
            return back()->with('error', 'Jika status Hadir/Telat, mohon pilih Tipe Absensi.');
        }

        $jamInput = $validated['jam'] ?: null;
        $jamDefaultMasuk  = '07:30:00';
        $jamDefaultPulang = '15:00:00';

        $attendance = Attendance::where('user_id', $validated['user_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($validated['tipe_absensi'] === 'check_in') {
            if ($attendance) {
                return back()->with('error', 'Data Check-In sudah ada. Gunakan Edit.');
            }

            $jamMasuk = $jamInput ?: $jamDefaultMasuk;

            Attendance::create([
                'user_id'       => $validated['user_id'],
                'tanggal'       => $validated['tanggal'],
                'check_in'      => $jamMasuk,
                'check_out'     => null,
                'status'        => $status,
                'keterangan'    => $validated['keterangan'] ?? null,
                'location'      => $validated['location'] ?? null,
                'location_name' => $lokasiNama,
            ]);

            return back()->with('success', 'Berhasil Check-In manual jam ' . $jamMasuk);
        }

        if ($validated['tipe_absensi'] === 'check_out') {
            $jamPulang = $jamInput ?: $jamDefaultPulang;

            if ($attendance) {
                $attendance->update([
                    'check_out'     => $jamPulang,
                    'status'        => $status,
                    'keterangan'    => $validated['keterangan'] ?? $attendance->keterangan,
                    'location'      => $validated['location'] ?? $attendance->location,
                    'location_name' => $validated['location_name'] ?? $attendance->location_name ?? $lokasiNama,
                ]);

                return back()->with('success', 'Berhasil update Check-Out jam ' . $jamPulang);
            }

            Attendance::create([
                'user_id'       => $validated['user_id'],
                'tanggal'       => $validated['tanggal'],
                'check_in'      => $jamDefaultMasuk,
                'check_out'     => $jamPulang,
                'status'        => $status,
                'keterangan'    => $validated['keterangan'] ?? null,
                'location'      => $validated['location'] ?? null,
                'location_name' => $lokasiNama,
            ]);

            return back()->with('success', 'Berhasil Check-In & Check-Out sekaligus.');
        }

        return back()->with('error', 'Tipe absensi tidak valid.');
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.attendance.edit', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'tanggal'       => 'required|date',
            'status'        => 'required|in:Hadir,Telat,Ijin,Sakit,Dinas Luar,Cuti,Tanpa Keterangan,Alpha',
            'check_in'      => 'nullable',
            'check_out'     => 'nullable',
            'location'      => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'keterangan'    => 'nullable|string|max:50', // ✅ untuk PC
        ]);

        $status = $validated['status'];
        if ($status === 'Alpha') $status = 'Tanpa Keterangan';

        $lokasiNama = $validated['location_name'] ?? $this->getDefaultLocationName($status);

        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'], true)) {
            $attendance->update([
                'tanggal'       => $validated['tanggal'],
                'check_in'      => null,
                'check_out'     => null,
                'status'        => $status,
                'keterangan'    => $validated['keterangan'] ?? null,
                'location'      => null,
                'location_name' => $lokasiNama,
            ]);
        } else {
            $request->validate(['check_in' => 'required']);

            $attendance->update([
                'tanggal'       => $validated['tanggal'],
                'check_in'      => $validated['check_in'],
                'check_out'     => $validated['check_out'] ?? null,
                'status'        => $status,
                'keterangan'    => $validated['keterangan'] ?? $attendance->keterangan,
                'location'      => $validated['location'] ?? null,
                'location_name' => $lokasiNama,
            ]);
        }

        return redirect()
            ->route('admin.students.show', $attendance->user_id)
            ->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi dihapus.');
    }
}

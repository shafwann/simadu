<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    // Helper untuk nama lokasi default
    private function getDefaultLocationName($status) {
        if ($status == 'Hadir' || $status == 'Telat') return 'KANTOR BAPPERIDA';
        if ($status == 'Sakit') return 'SAKIT';
        if ($status == 'Ijin') return 'IJIN';
        if ($status == 'Cuti') return 'CUTI';
        return 'LUAR KANTOR';
    }

    // 1. SIMPAN ABSENSI MANUAL (Create)
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'tanggal'       => 'required|date',
            'jam'           => 'required',
            'status'        => 'required',
            'location'      => 'nullable|string',
            'location_name' => 'nullable|string', // Validasi nama lokasi
        ]);

        // Normalisasi status legacy
        $status = $request->status;
        if ($status === 'Alpha') {
            $status = 'Tanpa Keterangan';
        }

        // Tentukan Nama Lokasi (Input User atau Default)
        $lokasiNama = $request->location_name ?? $this->getDefaultLocationName($status);

        // ===== KELOMPOK TIDAK HADIR (I/S/DL/CT/TK) =====
        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'])) {

            // Cek duplikasi harian
            $exists = Attendance::where('user_id', $request->user_id)
                ->whereDate('tanggal', $request->tanggal)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Data untuk tanggal ini sudah ada. Hapus dulu atau Edit.');
            }

            Attendance::create([
                'user_id'       => $request->user_id,
                'tanggal'       => $request->tanggal,
                'check_in'      => null,
                'check_out'     => null,
                'status'        => $status,
                'location'      => null,
                'location_name' => $lokasiNama, // Simpan Nama Lokasi
            ]);

            return back()->with('success', 'Berhasil input keterangan: ' . $status);
        }

        // ===== KELOMPOK HADIR / TELAT =====
        if (!$request->tipe_absensi) {
            return back()->with('error', 'Jika status Hadir/Telat, mohon pilih Tipe Absensi.');
        }

        $jamMasuk  = $request->jam ?? '07:30:00';
        $jamPulang = '15:00:00';

        $attendance = Attendance::where('user_id', $request->user_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        // LOGIKA CHECK IN
        if ($request->tipe_absensi === 'check_in') {
            if ($attendance) {
                return back()->with('error', 'Data Check-In sudah ada. Gunakan Edit.');
            }

            Attendance::create([
                'user_id'       => $request->user_id,
                'tanggal'       => $request->tanggal,
                'check_in'      => $jamMasuk,
                'check_out'     => null,
                'status'        => $status,
                'location'      => $request->location,
                'location_name' => $lokasiNama, // Simpan Nama Lokasi
            ]);

            return back()->with('success', 'Berhasil Check-In manual jam ' . $jamMasuk);
        }

        // LOGIKA CHECK OUT (Update record yang ada)
        if ($attendance) {
            $attendance->update([
                'check_out'     => $jamPulang,
                'status'        => $status,
                'location'      => $request->location ?? $attendance->location,
                // Update lokasi nama jika diisi baru, kalau tidak pakai yg lama
                'location_name' => $request->location_name ?? $attendance->location_name,
            ]);
            return back()->with('success', 'Berhasil update Check-Out jam ' . $jamPulang);
        }

        // CHECK IN & OUT SEKALIGUS (Jika belum ada record sama sekali)
        Attendance::create([
            'user_id'       => $request->user_id,
            'tanggal'       => $request->tanggal,
            'check_in'      => $jamMasuk,
            'check_out'     => $jamPulang,
            'status'        => $status,
            'location'      => $request->location,
            'location_name' => $lokasiNama, // Simpan Nama Lokasi
        ]);

        return back()->with('success', 'Berhasil Check-In & Check-Out sekaligus.');
    }

    // 2. FORM EDIT ABSENSI
    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.attendance.edit', compact('attendance'));
    }

    // 3. PROSES UPDATE ABSENSI
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'tanggal'       => 'required|date',
            'status'        => 'required|in:Hadir,Telat,Ijin,Sakit,Dinas Luar,Cuti,Tanpa Keterangan,Alpha',
            'check_in'      => 'nullable',
            'check_out'     => 'nullable',
            'location'      => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255', // ✅ Tambahkan validasi ini
        ]);

        $status = $request->status;
        if ($status === 'Alpha') {
            $status = 'Tanpa Keterangan';
        }

        // Tentukan nama lokasi (Ambil input, atau fallback ke default status)
        $lokasiNama = $request->location_name ?? $this->getDefaultLocationName($status);

        // ===== KELOMPOK TIDAK HADIR =====
        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'])) {
            $attendance->update([
                'tanggal'       => $request->tanggal,
                'check_in'      => null,
                'check_out'     => null,
                'status'        => $status,
                'location'      => null,
                'location_name' => $lokasiNama, // Update nama lokasi
            ]);
        }
        // ===== KELOMPOK HADIR / TELAT =====
        else {
            $request->validate([
                'check_in' => 'required',
            ]);

            $attendance->update([
                'tanggal'       => $request->tanggal,
                'check_in'      => $request->check_in,
                'check_out'     => $request->check_out,
                'status'        => $status,
                'location'      => $request->location,
                'location_name' => $lokasiNama, // Update nama lokasi
            ]);
        }

        return redirect()
            ->route('admin.students.show', $attendance->user_id)
            ->with('success', 'Data absensi berhasil diperbarui.');
    }

    // 4. HAPUS ABSENSI
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi dihapus.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    // 1. SIMPAN ABSENSI MANUAL (Create)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'tipe_absensi' => 'required', // Input dari dropdown baru
            'status' => 'required',
        ]);

        // Tentukan Jam Berdasarkan Pilihan Dropdown
        $jamMasuk = '07:30:00';
        $jamPulang = '15:00:00';

        // Cek apakah sudah ada data absen di tanggal & user tersebut?
        $attendance = Attendance::where('user_id', $request->user_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($request->tipe_absensi == 'check_in') {
            // --- LOGIKA CHECK IN (07:30) ---
            if ($attendance) {
                return back()->with('error', 'Data Check-In untuk tanggal ini sudah ada. Gunakan tombol Edit jika ingin mengubah.');
            }

            Attendance::create([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                'check_in' => $jamMasuk,   // Set 07:30
                'check_out' => null,       // Belum pulang
                'status' => $request->status,
            ]);

            $pesan = 'Berhasil Check-In manual jam ' . $jamMasuk;

        } else {
            // --- LOGIKA CHECK OUT (15:00) ---
            if ($attendance) {
                // Jika sudah ada data (misal sudah checkin pagi), update jam pulangnya saja
                $attendance->update([
                    'check_out' => $jamPulang, // Set 15:00
                    'status' => $request->status // Update status jika perlu
                ]);
                $pesan = 'Berhasil update Check-Out jam ' . $jamPulang;
            } else {
                // Jika belum ada data sama sekali, buat Full Day (Masuk 07:30 & Pulang 15:00)
                Attendance::create([
                    'user_id' => $request->user_id,
                    'tanggal' => $request->tanggal,
                    'check_in' => $jamMasuk,    // Asumsikan masuk pagi
                    'check_out' => $jamPulang,  // Dan pulang sore
                    'status' => $request->status,
                ]);
                $pesan = 'Data kosong. Berhasil dibuat Check-In (07:30) & Check-Out (15:00).';
            }
        }

        return back()->with('success', $pesan);
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
            'tanggal' => 'required|date',
            'check_in' => 'required',
            'check_out' => 'nullable',
            'status' => 'required',
        ]);

        // Update data tanpa batasan waktu/lokasi
        $attendance->update([
            'tanggal' => $request->tanggal,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => $request->status,
        ]);

        // Redirect kembali ke halaman detail mahasiswa
        return redirect()->route('admin.students.show', $attendance->user_id)
            ->with('success', 'Data absensi berhasil diperbarui oleh Admin.');
    }

    // 4. HAPUS ABSENSI
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi dihapus.');
    }
}

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
            'status' => 'required',
            'tipe_absensi' => 'nullable',
            'location' => 'nullable|string|max:255', // <--- BARU: Validasi Lokasi
        ]);

        // --- KELOMPOK 1: KETIDAKHADIRAN (Alpha, Sakit, Izin) ---
        if (in_array($request->status, ['Alpha', 'Sakit', 'Izin'])) {

            $exists = Attendance::where('user_id', $request->user_id)
                ->whereDate('tanggal', $request->tanggal)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Data untuk tanggal ini sudah ada. Hapus dulu atau Edit.');
            }

            Attendance::create([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                'check_in' => null,
                'check_out' => null,
                'status' => $request->status,
                'location' => null, // Tidak perlu lokasi kalau tidak hadir
            ]);

            return back()->with('success', 'Berhasil input keterangan: ' . $request->status);
        }

        // --- KELOMPOK 2: KEHADIRAN FISIK (Hadir, Telat) ---
        else {
            if (!$request->tipe_absensi) {
                return back()->with('error', 'Jika status Hadir/Telat, mohon pilih Tipe Absensi.');
            }

            $jamMasuk = $request->jam ?? '07:30:00'; // Gunakan input jam manual atau default
            $jamPulang = '15:00:00';

            $attendance = Attendance::where('user_id', $request->user_id)
                ->whereDate('tanggal', $request->tanggal)
                ->first();

            // JIKA PILIH CHECK IN (PAGI)
            if ($request->tipe_absensi == 'check_in') {
                if ($attendance) {
                    return back()->with('error', 'Data Check-In sudah ada. Gunakan Edit.');
                }

                Attendance::create([
                    'user_id' => $request->user_id,
                    'tanggal' => $request->tanggal,
                    'check_in' => $jamMasuk,
                    'check_out' => null,
                    'status' => $request->status,
                    'location' => $request->location, // <--- BARU: Simpan Lokasi
                ]);

                return back()->with('success', 'Berhasil Check-In manual jam ' . $jamMasuk);
            }

            // JIKA PILIH CHECK OUT (SORE)
            else {
                if ($attendance) {
                    $attendance->update([
                        'check_out' => $jamPulang,
                        'status' => $request->status,
                        // Update lokasi jika diisi (opsional, biasanya lokasi checkout beda)
                        'location' => $request->location ?? $attendance->location
                    ]);
                    $pesan = 'Berhasil update Check-Out jam ' . $jamPulang;
                } else {
                    // Buat Full Day
                    Attendance::create([
                        'user_id' => $request->user_id,
                        'tanggal' => $request->tanggal,
                        'check_in' => $jamMasuk,
                        'check_out' => $jamPulang,
                        'status' => $request->status,
                        'location' => $request->location, // <--- BARU
                    ]);
                    $pesan = 'Berhasil Check-In & Check-Out sekaligus.';
                }
                return back()->with('success', $pesan);
            }
        }
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
            'status' => 'required|in:Hadir,Telat,Sakit,Izin,Alpha',
            'location' => 'nullable|string|max:255', // <--- BARU
        ]);

        // ===== KELOMPOK TIDAK HADIR =====
        if (in_array($request->status, ['Alpha', 'Sakit', 'Izin'])) {
            $attendance->update([
                'tanggal' => $request->tanggal,
                'check_in' => null,
                'check_out' => null,
                'status' => $request->status,
                'location' => null,
            ]);
        }
        // ===== KELOMPOK HADIR / TELAT =====
        else {
                    $request->validate([
                        'check_in' => 'required',
                        'check_out' => 'nullable',
                    ]);

                    $attendance->update([
                        'tanggal' => $request->tanggal,
                        'check_in' => $request->check_in,
                        'check_out' => $request->check_out,
                        'status' => $request->status,
                        'location' => $request->location, // Update Lokasi sudah ada
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

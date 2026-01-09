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
            'status' => 'required|in:Hadir,Telat,Ijin,Sakit,Dinas Luar,Cuti,Tanpa Keterangan,Alpha',
            'tipe_absensi' => 'nullable|in:check_in,check_out',
            'jam' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        // normalisasi status legacy
        $status = $request->status;
        if ($status === 'Alpha') {
            $status = 'Tanpa Keterangan'; // legacy -> TK
        }

        // ===== kelompok TIDAK HADIR (I/S/DL/CT/TK) =====
        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'])) {

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
                'status' => $status,
                'location' => null,
            ]);

            return back()->with('success', 'Berhasil input keterangan: ' . $status);
        }

        // ===== kelompok HADIR / TELAT =====
        // wajib pilih tipe_absensi
        if (!$request->tipe_absensi) {
            return back()->with('error', 'Jika status Hadir/Telat, mohon pilih Tipe Absensi.');
        }

        $jamMasuk  = $request->jam ?? '07:30:00';
        $jamPulang = '15:00:00';

        $attendance = Attendance::where('user_id', $request->user_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        // CHECK IN
        if ($request->tipe_absensi === 'check_in') {
            if ($attendance) {
                return back()->with('error', 'Data Check-In sudah ada. Gunakan Edit.');
            }

            Attendance::create([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                'check_in' => $jamMasuk,
                'check_out' => null,
                'status' => $status, // Hadir / Telat
                'location' => $request->location,
            ]);

            return back()->with('success', 'Berhasil Check-In manual jam ' . $jamMasuk);
        }

        // CHECK OUT
        if ($attendance) {
            $attendance->update([
                'check_out' => $jamPulang,
                'status' => $status,
                'location' => $request->location ?? $attendance->location,
            ]);
            return back()->with('success', 'Berhasil update Check-Out jam ' . $jamPulang);
        }

        // jika belum ada record, buat full day
        Attendance::create([
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'check_in' => $jamMasuk,
            'check_out' => $jamPulang,
            'status' => $status,
            'location' => $request->location,
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
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Telat,Ijin,Sakit,Dinas Luar,Cuti,Tanpa Keterangan,Alpha',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'location' => 'nullable|string|max:255',
        ]);

        $status = $request->status;
        if ($status === 'Alpha') {
            $status = 'Tanpa Keterangan';
        }

        // ===== kelompok TIDAK HADIR =====
        if (in_array($status, ['Tanpa Keterangan', 'Sakit', 'Ijin', 'Dinas Luar', 'Cuti'])) {
            $attendance->update([
                'tanggal' => $request->tanggal,
                'check_in' => null,
                'check_out' => null,
                'status' => $status,
                'location' => null,
            ]);
        } else {
            // ===== kelompok HADIR / TELAT =====
            // kalau hadir/telat, minimal check_in harus ada
            $request->validate([
                'check_in' => 'required',
            ]);

            $attendance->update([
                'tanggal' => $request->tanggal,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'status' => $status,
                'location' => $request->location,
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

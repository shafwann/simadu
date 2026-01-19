<?php

namespace App\Http\Controllers;

use App\Models\PembimbingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DosenRequestController extends Controller
{
    public function create()
    {
        return view('mahasiswa.pembimbing.request', [
            'mahasiswa' => auth()->user(),
            'request' => PembimbingRequest::where('mahasiswa_id', auth()->id())->latest()->first()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string',
            'nidn'       => 'nullable|digits:10',
            'email'      => 'required|email|unique:pembimbing_requests',
            'password'   => 'required|min:8',
        ]);

        PembimbingRequest::create([
            'nama_dosen'        => $request->nama_dosen,
            'nidn'              => $request->nidn,
            'email'             => $request->email,
            'password'          => bcrypt($request->password),
            'mahasiswa_id'      => auth()->id(),
            'instansi'          => auth()->user()->instansi,
            'prodi'             => auth()->user()->prodi,
            'fakultas'          => auth()->user()->fakultas,
            'kategori_instansi' => auth()->user()->kategori_instansi,
            'status'            => 'pending',
        ]);

        return back()->with('success', 'Pengajuan dikirim');
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $req = PembimbingRequest::with('mahasiswa')->findOrFail($id);

            // 1️⃣ Cegah approve ulang
            if ($req->status !== 'pending') {
                abort(403, 'Request sudah diproses');
            }

            // 2️⃣ VALIDASI INSTANSI BERDASARKAN MAHASISWA
            if (
                auth()->user()->kategori_instansi &&
                $req->mahasiswa->kategori_instansi !== auth()->user()->kategori_instansi
            ) {
                abort(403, 'Akses Ditolak. Beda instansi.');
            }

            // 3️⃣ BUAT / AMBIL DOSEN
            $dosen = User::firstOrCreate(
                ['email' => $req->email],
                [
                    'name'              => $req->nama_dosen,
                    'password'          => $req->password,
                    'role'              => 'pembimbing',
                    'status'            => 'aktif',
                    'kategori_instansi' => $req->mahasiswa->kategori_instansi, // 🔑
                ]
            );

            // 4️⃣ HUBUNGKAN DOSEN ↔ MAHASISWA
            $req->mahasiswa
                ->dosenPembimbing()
                ->syncWithoutDetaching([$dosen->id]);

            // 5️⃣ UPDATE STATUS
            $req->update(['status' => 'approved']);
        });

        return back()->with('success', 'Request disetujui');
    }


    public function reject($id)
    {
        $req = PembimbingRequest::with('mahasiswa')->findOrFail($id);

        // 1️⃣ Cegah reject ulang
        if ($req->status !== 'pending') {
            abort(403, 'Request sudah diproses');
        }

        // 2️⃣ VALIDASI INSTANSI (PAKAI MAHASISWA)
        if (
            auth()->user()->kategori_instansi &&
            $req->mahasiswa->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        // 3️⃣ UPDATE STATUS
        $req->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Request ditolak');
    }

}

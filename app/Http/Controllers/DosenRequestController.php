<?php

namespace App\Http\Controllers;

use App\Models\PembimbingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'nama_dosen' => 'required|string|max:255',
            'nidn'       => 'required',
            'email'      => 'required|email',
            'password'   => 'nullable|min:8',
        ]);

        $mahasiswa = auth()->user();

        $dosen = User::where('nidn', $request->nidn)->first();

        if ($dosen) {

            // DOSEN SUDAH ADA
            PembimbingRequest::create([
                'nama_dosen'   => $dosen->name,
                'nidn'         => $dosen->nidn,
                'email'        => $dosen->email,
                'mahasiswa_id' => $mahasiswa->id,
                'status'       => 'pending',
            ]);
        } else {

            // DOSEN BARU
            PembimbingRequest::create([
                'nama_dosen'   => $request->nama_dosen,
                'nidn'         => $request->nidn,
                'email'        => $request->email,
                'password'     => bcrypt($request->password),
                'mahasiswa_id' => $mahasiswa->id,
                'instansi'          => auth()->user()->instansi,
                'prodi'             => auth()->user()->prodi,
                'fakultas'          => auth()->user()->fakultas,
                'kategori_instansi' => auth()->user()->kategori_instansi,
                'status'       => 'pending',
            ]);
        }

        return back()->with('success', 'Pengajuan dikirim.');
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $req = PembimbingRequest::with('mahasiswa')->findOrFail($id);

            if ($req->status !== 'pending') {
                abort(403, 'Request sudah diproses');
            }

            if (
                auth()->user()->kategori_instansi &&
                $req->mahasiswa->kategori_instansi !== auth()->user()->kategori_instansi
            ) {
                abort(403, 'Akses Ditolak. Beda instansi.');
            }

            // 🔎 Cari berdasarkan NIDN (bukan email)
            $dosen = User::withoutGlobalScope(\App\Models\Scopes\AgencyScope::class)
                ->where('nidn', $req->nidn)
                ->first();

            if (!$dosen) {

                // DOSEN BARU → password WAJIB ada
                if (!$req->password) {
                    abort(500, 'Password kosong untuk dosen baru.');
                }

                $dosen = User::create([
                    'name'              => $req->nama_dosen,
                    'email'             => $req->email,
                    'nidn'              => $req->nidn,
                    'password'          => $req->password, // sudah hash
                    'role'              => 'pembimbing',
                    'status'            => 'aktif',
                    'kategori_instansi' => $req->mahasiswa->kategori_instansi,
                    'instansi'          => $req->mahasiswa->instansi,
                    'prodi'             => $req->mahasiswa->prodi,
                    'fakultas'          => $req->mahasiswa->fakultas,
                    'bidang'            => $req->mahasiswa->bidang,
                ]);
            } else {

                // DOSEN SUDAH ADA → jangan sentuh password
                $dosen->update([
                    'name'   => $req->nama_dosen ?? $dosen->name,
                    'status' => 'aktif',
                ]);
            }

            // Hubungkan dosen ke mahasiswa
            $req->mahasiswa
                ->dosenPembimbing()
                ->syncWithoutDetaching([$dosen->id]);

            $req->update(['status' => 'approved']);
        });

        return back()->with('success', 'Request disetujui');
    }

    public function reject($id)
    {
        $req = PembimbingRequest::with('mahasiswa')->findOrFail($id);

        // Cegah reject ulang
        if ($req->status !== 'pending') {
            abort(403, 'Request sudah diproses');
        }

        // VALIDASI INSTANSI (PAKAI MAHASISWA)
        if (
            auth()->user()->kategori_instansi &&
            $req->mahasiswa->kategori_instansi !== auth()->user()->kategori_instansi
        ) {
            abort(403, 'Akses Ditolak. Beda instansi.');
        }

        // UPDATE STATUS
        $req->update([
            'status' => 'rejected'
        ]);

        return back()->with('success', 'Request ditolak');
    }

    // search dosen untuk autocomplete
    public function search(Request $request)
    {
        $query = $request->q;

        return User::where('role', 'pembimbing')
            ->where('name', 'like', "%{$query}%")
            ->where('status', 'aktif')
            ->where('instansi', auth()->user()->instansi)
            ->where('fakultas', auth()->user()->fakultas)
            ->select('id', 'name', 'email', 'nidn')
            ->limit(10)
            ->get();
    }
}

<?php 
namespace App\Http\Controllers;

use App\Models\PembimbingRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; 

class DosenRequestController extends Controller
{

    //create
    public function create()
    {
        $mahasiswa = auth()->user();

        $request = PembimbingRequest::where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->first();

        return view('mahasiswa.pembimbing.request', [
            'mahasiswa' => $mahasiswa,
            'request' => $request
        ]);
    }



    // Simpan request mahasiswa
    public function store(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'nidn' => 'nullable|digits:10',
            'email' => 'required|email|unique:pembimbing_requests,email',
            'password' => 'required|min:8',
        ]);

        PembimbingRequest::create([
            'nama_dosen' => $request->nama_dosen,
            'nidn' => $request->nidn,
            'email' => $request->email,
             'password' => Hash::make($request->password),
            'instansi' => auth()->user()->instansi,
            'prodi' => auth()->user()->prodi,
            'fakultas' => auth()->user()->fakultas,
            'mahasiswa_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan pembimbing berhasil dikirim.');
    }

    // Approve request dosen → pindah ke users + hubungkan ke mahasiswa
public function approve($id)
{
    DB::transaction(function () use ($id) {

        $request = PembimbingRequest::findOrFail($id);

        // ❌ CEGAH APPROVE ULANG
        if ($request->status !== 'pending') {
            abort(403, 'Request sudah diproses');
        }

        // 🔍 CEK APAKAH DOSEN SUDAH ADA
        $dosen = User::where('email', $request->email)->first();

        if (! $dosen) {
            $dosen = User::create([
                'name'      => $request->nama_dosen,
                'nidn'      => $request->nidn,
                'email'     => $request->email,
                'password'  => $request->password, // sudah hash
                'instansi'  => $request->instansi,
                'prodi'     => $request->prodi,
                'fakultas'  => $request->fakultas,
                'role'      => 'pembimbing',
                'status'    => 'aktif',
            ]);
        }

        // 🔗 HUBUNGKAN DOSEN ↔ MAHASISWA
        $mahasiswa = User::findOrFail($request->mahasiswa_id);
        $mahasiswa->dosenPembimbing()
                  ->syncWithoutDetaching([$dosen->id]);

        // ✅ UPDATE STATUS REQUEST
        $request->update([
            'status' => 'approved'
        ]);
    });

    return back()->with('success', 'Dosen berhasil disetujui.');
}




    // Tolak request

public function reject($id)
{
    $request = PembimbingRequest::findOrFail($id);

    $request->update([
        'status' => 'rejected'
    ]);

    return back()->with('success', 'Pengajuan dosen ditolak');
}

}

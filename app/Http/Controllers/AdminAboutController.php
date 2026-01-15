<?php

namespace App\Http\Controllers;

use App\Models\AboutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAboutController extends Controller
{
    private function checkSuperAdmin(): void
    {
        // Super Admin kamu ditandai bidang = "Administrator Sistem"
        if (Auth::user()->bidang !== 'Administrator Sistem') {
            abort(403, 'Akses ditolak. Hanya Super Admin yang boleh mengubah About.');
        }
    }

    public function index()
    {
        $this->checkSuperAdmin();

        $about = AboutSetting::firstOrFail();

        return view('admin.about.index', compact('about'));
    }


    public function update(Request $request)
    {
        $this->checkSuperAdmin();

        $data = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:255'],
            'kepala_instansi' => ['required', 'string', 'max:255'],
            'nip_kepala_instansi' => ['required', 'string', 'max:50'],
            'jam_operasional' => ['required', 'string', 'max:255'],

            'app_name' => ['nullable', 'string', 'max:50'],
            'agency_name' => ['nullable', 'string', 'max:50'],
            'app_description' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],

            // ======================
            // LOKASI KANTOR (ABSENSI GPS)
            // ======================
            'office_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'office_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'office_name' => ['nullable', 'string', 'max:255'],
            'radius_meter' => ['required', 'integer', 'min:10', 'max:100000'],

            // ======================
            // CHECK-IN
            // ======================
            'senin_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'senin_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'selasa_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'selasa_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'rabu_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'rabu_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'kamis_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'kamis_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'jumat_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'jumat_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            // ======================
            // CHECK-OUT
            // ======================
            'senin_checkout_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'senin_checkout_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'selasa_checkout_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'selasa_checkout_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'rabu_checkout_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'rabu_checkout_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'kamis_checkout_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'kamis_checkout_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'jumat_checkout_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'jumat_checkout_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        // ======================
        // NORMALISASI TIME
        // ======================
        $timeFields = array_keys($data);
        foreach ($timeFields as $f) {
            if (isset($data[$f]) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $data[$f])) {
                $data[$f] = substr($data[$f], 0, 5);
            }
        }

        // ======================
        // VALIDASI MANUAL
        // ======================
        $pairs = [
            ['mulai' => 'senin_mulai', 'selesai' => 'senin_selesai', 'label' => 'Senin Check-in'],
            ['mulai' => 'selasa_mulai', 'selesai' => 'selasa_selesai', 'label' => 'Selasa Check-in'],
            ['mulai' => 'rabu_mulai', 'selesai' => 'rabu_selesai', 'label' => 'Rabu Check-in'],
            ['mulai' => 'kamis_mulai', 'selesai' => 'kamis_selesai', 'label' => 'Kamis Check-in'],
            ['mulai' => 'jumat_mulai', 'selesai' => 'jumat_selesai', 'label' => 'Jumat Check-in'],

            ['mulai' => 'senin_checkout_mulai', 'selesai' => 'senin_checkout_selesai', 'label' => 'Senin Check-out'],
            ['mulai' => 'selasa_checkout_mulai', 'selesai' => 'selasa_checkout_selesai', 'label' => 'Selasa Check-out'],
            ['mulai' => 'rabu_checkout_mulai', 'selesai' => 'rabu_checkout_selesai', 'label' => 'Rabu Check-out'],
            ['mulai' => 'kamis_checkout_mulai', 'selesai' => 'kamis_checkout_selesai', 'label' => 'Kamis Check-out'],
            ['mulai' => 'jumat_checkout_mulai', 'selesai' => 'jumat_checkout_selesai', 'label' => 'Jumat Check-out'],
        ];

        foreach ($pairs as $p) {
            if ($data[$p['selesai']] <= $data[$p['mulai']]) {
                return back()
                    ->withErrors([$p['selesai'] => "{$p['label']} (Selesai) harus lebih besar dari (Mulai)."])
                    ->withInput();
            }
        }

        $about = AboutSetting::firstOrFail();
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada (opsional, perlu import Storage)
            // if ($about->logo_path && \Storage::exists('public/' . $about->logo_path)) {
            //     \Storage::delete('public/' . $about->logo_path);
            // }

            // Simpan logo baru di folder public/uploads/logo
            $path = $request->file('logo')->store('uploads/logo', 'public');
            $data['logo_path'] = $path;
        }

        // Update data (sisa logika sama seperti sebelumnya)
        // Hapus key 'logo' dari array data karena kolom di DB namanya 'logo_path'
        unset($data['logo']);

        $about->update($data);

        return back()->with('success', 'Data About berhasil diperbarui.');
    }
}

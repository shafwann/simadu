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

        // Konversi JSON bidang_options menjadi text per baris untuk ditampilkan di textarea
        $bidangOptionsText = '';
        if (!empty($about->bidang_options)) {
            $arr = json_decode($about->bidang_options, true);
            if (is_array($arr)) {
                $bidangOptionsText = implode("\n", $arr);
            }
        }

        return view('admin.about.index', compact('about', 'bidangOptionsText'));
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
        $section = $request->input('section'); // Mengambil input hidden 'section' dari form
        $data = [];

        // =========================================================
        // 1. VALIDASI BERDASARKAN SECTION (KATEGORI)
        // =========================================================
        switch ($section) {
            case 'branding':
                $data = $request->validate([
                    'app_name' => ['nullable', 'string', 'max:50'],
                    'agency_name' => ['nullable', 'string', 'max:50'],
                    'app_description' => ['nullable', 'string', 'max:100'],
                    'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                ]);
                break;

            case 'bidang':
                $data = $request->validate([
                    'bidang_options' => ['nullable', 'string'],
                ]);
                break;

            case 'instansi':
                $data = $request->validate([
                    'nama_instansi' => ['required', 'string', 'max:255'],
                    'kepala_instansi' => ['required', 'string', 'max:255'],
                    'nip_kepala_instansi' => ['required', 'string', 'max:50'],
                    'jam_operasional' => ['required', 'string', 'max:255'],
                ]);
                break;

            case 'lokasi':
                $data = $request->validate([
                    'office_name' => ['nullable', 'string', 'max:255'],
                    'office_latitude' => ['nullable', 'numeric', 'between:-90,90'],
                    'office_longitude' => ['nullable', 'numeric', 'between:-180,180'],
                    'radius_meter' => ['required', 'integer', 'min:10', 'max:1000'],
                ]);
                break;

            case 'jadwal':
                // Validasi semua field waktu check-in dan check-out
                $rules = [];
                $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
                foreach ($days as $day) {
                    $rules["{$day}_mulai"] = ['required'];
                    $rules["{$day}_selesai"] = ['required'];
                    $rules["{$day}_checkout_mulai"] = ['required'];
                    $rules["{$day}_checkout_selesai"] = ['required'];
                }
                $data = $request->validate($rules);
                break;

            // TAMBAHAN: SECTION FOOTER
            case 'footer':
                $data = $request->validate([
                    'office_address' => ['nullable', 'string'],
                    'office_phone' => ['nullable', 'string', 'max:20'],
                    'office_email' => ['nullable', 'email'],

                    // Sosmed Lengkap
                    'social_instagram' => ['nullable', 'url'],
                    'social_facebook' => ['nullable', 'url'],
                    'social_youtube' => ['nullable', 'url'],
                    'social_x' => ['nullable', 'url'],

                    'google_maps_embed' => ['nullable', 'string'],

                    // Link Dinamis (Repeater)
                    'links_label' => ['nullable', 'array'],
                    'links_url' => ['nullable', 'array'],
                ]);

                // Proses Link Dinamis ke JSON
                $customLinks = [];
                if ($request->has('links_label') && $request->has('links_url')) {
                    foreach ($request->links_label as $index => $label) {
                        if (!empty($label) && !empty($request->links_url[$index])) {
                            $customLinks[] = [
                                'label' => $label,
                                'url' => $request->links_url[$index]
                            ];
                        }
                    }
                }
                $data['custom_links'] = !empty($customLinks) ? json_encode($customLinks) : null;

                unset($data['links_label']);
                unset($data['links_url']);
                break;

            default:
                return back()->with('error', 'Kategori penyimpanan tidak dikenali.');
        }

        // =========================================================
        // 2. LOGIKA KHUSUS PER SECTION
        // =========================================================

        // --- A. Section Branding: Upload Logo ---
        if ($section === 'branding' && $request->hasFile('logo')) {
            // Hapus logo lama jika ada (opsional)
            // if ($about->logo_path && \Storage::exists('public/' . $about->logo_path)) {
            //     \Storage::delete('public/' . $about->logo_path);
            // }

            // Simpan logo baru
            $path = $request->file('logo')->store('uploads/logo', 'public');
            $data['logo_path'] = $path;

            // Hapus key 'logo' agar tidak error saat update DB (karena nama kolomnya logo_path)
            unset($data['logo']);
        }

        // --- B. Section Bidang: Convert Text ke JSON ---
        if ($section === 'bidang' && array_key_exists('bidang_options', $data)) {
            $raw = $data['bidang_options'];
            // Pecah string berdasarkan baris baru
            $arr = collect(preg_split("/\r\n|\n|\r/", (string) $raw))
                ->map(fn ($x) => trim($x)) // Hapus spasi depan/belakang
                ->filter() // Hapus baris kosong
                ->values() // Reindex array
                ->all();

            $data['bidang_options'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // --- C. Section Jadwal: Normalisasi & Validasi Logika Waktu ---
        if ($section === 'jadwal') {
            // 1. Normalisasi (Hapus detik jika ada, misal 08:00:00 jadi 08:00)
            foreach ($data as $key => $val) {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $val)) {
                    $data[$key] = substr($val, 0, 5);
                }
            }

            // 2. Validasi Manual: Jam Selesai harus > Jam Mulai
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
        }

        // =========================================================
        // 3. UPDATE DATABASE
        // =========================================================
        $about->update($data);

        return back()->with('success', 'Data bagian ' . strtoupper($section) . ' berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AboutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminAboutController extends Controller
{
    private function checkAccess(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin Instansi yang boleh mengubah About.');
        }
    }

    public function index()
    {
        $this->checkAccess();
        $user = Auth::user();
        $kategori = $user->kategori_instansi ?? 'UMUM';

        // Gunakan firstOrCreate agar data muncul di form pertama kali
        $about = AboutSetting::firstOrCreate(
            ['kategori_instansi' => $kategori],
            [
                'app_name' => $kategori,
                'agency_name' => 'Pemkab Karanganyar',
                'nama_instansi' => 'Dinas ' . $kategori,
                'app_description' => 'Sistem Informasi Magang ' . $kategori,
                'kepala_instansi' => '-',
                'nip_kepala_instansi' => '-',
                'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
                'radius_meter' => 50
            ]
        );

        // Konversi JSON bidang_options
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
        $this->checkAccess();
        $user = Auth::user();
        $kategori = $user->kategori_instansi ?? 'UMUM';

        // Validasi Input
        $section = $request->input('section');
        $data = [];

        // --- VALIDASI (Sama seperti sebelumnya) ---
        switch ($section) {
            case 'branding':
                $data = $request->validate([
                    'app_name' => ['nullable', 'string', 'max:50'],
                    'agency_name' => ['nullable', 'string', 'max:50'], 
                    'app_description' => ['nullable', 'string', 'max:100'],
                    'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5048'],
                ]);
                break;

            case 'bidang':
                $data = $request->validate(['bidang_options' => ['nullable', 'string']]);
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
                    'radius_meter' => ['required', 'integer', 'min:10', 'max:100000'],
                ]);
                break;

            case 'jadwal':
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

            case 'footer':
                $data = $request->validate([
                    'office_address' => ['nullable', 'string'],
                    'office_phone' => ['nullable', 'string', 'max:20'],
                    'office_email' => ['nullable', 'email'],
                    'social_instagram' => ['nullable', 'url'],
                    'social_facebook' => ['nullable', 'url'],
                    'social_youtube' => ['nullable', 'url'],
                    'social_x' => ['nullable', 'url'],
                    'google_maps_embed' => ['nullable', 'string'],
                    'whatsapp_number' => ['nullable', 'numeric'],
                    'links_label' => ['nullable', 'array'],
                    'links_url' => ['nullable', 'array'],
                ]);

                // Proses Link Dinamis
                $customLinks = [];
                if ($request->has('links_label') && $request->has('links_url')) {
                    foreach ($request->links_label as $index => $label) {
                        if (!empty($label) && !empty($request->links_url[$index])) {
                            $customLinks[] = ['label' => $label, 'url' => $request->links_url[$index]];
                        }
                    }
                }
                $data['custom_links'] = !empty($customLinks) ? json_encode($customLinks) : null;
                unset($data['links_label'], $data['links_url']);
                break;

            default:
                return back()->with('error', 'Kategori penyimpanan tidak dikenali.');
        }

        // --- PROSES DATA ---

        // 1. Ambil Data Lama (Penting untuk hapus logo lama)
        // Gunakan firstOrNew agar jika belum ada tidak error
        $about = AboutSetting::firstOrNew(['kategori_instansi' => $kategori]);

        // Handle Logo Upload
        if ($section === 'branding' && $request->hasFile('logo')) {
            // Hapus logo lama jika ada & file eksis
            if ($about->exists && $about->logo_path && Storage::disk('public')->exists($about->logo_path)) {
                Storage::disk('public')->delete($about->logo_path);
            }

            $folderName = 'uploads/logo/' . ($kategori ?? 'umum');
            $path = $request->file('logo')->store($folderName, 'public');
            $data['logo_path'] = $path;
            unset($data['logo']);
        }

        // Handle JSON Bidang
        if ($section === 'bidang' && isset($data['bidang_options'])) {
            $raw = $data['bidang_options'];
            $arr = collect(preg_split("/\r\n|\n|\r/", (string) $raw))
                ->map(fn ($x) => trim($x))->filter()->values()->all();
            $data['bidang_options'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // Handle Time (Jadwal)
        if ($section === 'jadwal') {
            foreach ($data as $key => $val) {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $val)) $data[$key] = substr($val, 0, 5);
            }
            // (Validasi manual jam mulai < selesai bisa ditambahkan di sini)
        }

        // ✅ INI PERBAIKANNYA: Pakai updateOrCreate
        // Ini memastikan data diupdate jika ketemu kategori_instansi yang sama,
        // atau dibuat baru jika belum ada.
        AboutSetting::updateOrCreate(
            ['kategori_instansi' => $kategori], // Cari berdasarkan ini
            $data // Update dengan data ini
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}

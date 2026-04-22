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

        // Pastikan record per kategori ada
        $about = AboutSetting::firstOrCreate(
            ['kategori_instansi' => $kategori],
            [
                // Branding default
                'app_name'        => $kategori,
                'agency_name'     => 'Pemkab Karanganyar',
                'app_description' => 'Sistem Informasi Magang ' . $kategori,

                // Identitas default
                'nama_instansi'        => 'Dinas ' . $kategori,
                'kepala_instansi'      => '-',
                'nip_kepala_instansi'  => '-',
                'jam_operasional'      => 'Senin-Jumat: 08.00-16.00',

                // Lokasi default
                'office_name'      => null,
                'office_latitude'  => null,
                'office_longitude' => null,
                'radius_meter'     => 50,
            ]
        );

        // Konversi JSON bidang_options -> teks multi baris
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

        $section = $request->input('section');
        $data = [];

        switch ($section) {
            case 'branding':
                $data = $request->validate([
                    'app_name'        => ['nullable', 'string', 'max:50'],
                    'agency_name'     => ['nullable', 'string', 'max:50'],
                    'app_description' => ['nullable', 'string', 'max:100'],
                    'logo'            => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5048'],
                ]);
                break;

            case 'bidang':
                $data = $request->validate([
                    'bidang_options' => ['nullable', 'string'],
                ]);
                break;

            case 'instansi':
                $data = $request->validate([
                    'nama_instansi'       => ['required', 'string', 'max:255'],
                    'kepala_instansi'     => ['required', 'string', 'max:255'],
                    'nip_kepala_instansi' => ['required', 'string', 'max:50'],
                ]);
                break;

            case 'lokasi':
                $data = $request->validate([
                    'office_name'      => ['nullable', 'string', 'max:255'],
                    'office_latitude'  => ['nullable', 'numeric', 'between:-90,90'],
                    'office_longitude' => ['nullable', 'numeric', 'between:-180,180'],
                    'radius_meter'     => ['required', 'integer', 'min:10', 'max:100000'],
                ]);
                break;

            case 'jadwal':
                $rules = [];
                $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
                foreach ($days as $day) {
                    $rules["{$day}_mulai"]            = ['required'];
                    $rules["{$day}_selesai"]          = ['required'];
                    $rules["{$day}_checkout_mulai"]   = ['required'];
                    $rules["{$day}_checkout_selesai"] = ['required'];
                }
                $data = $request->validate($rules);
                break;

            case 'footer':
                $data = $request->validate([
                    'office_address'    => ['nullable', 'string'],
                    'office_phone'      => ['nullable', 'string', 'max:20'],
                    'office_email'      => ['nullable', 'email'],
                    'social_instagram'  => ['nullable', 'string', 'max:255'],
                    'social_facebook'   => ['nullable', 'string', 'max:255'],
                    'social_youtube'    => ['nullable', 'string', 'max:255'],
                    'social_x'          => ['nullable', 'string', 'max:255'],
                    'google_maps_embed' => ['nullable', 'string'],
                    'whatsapp_number'   => ['nullable', 'string', 'max:20'],
                    'links_label'       => ['nullable', 'array'],
                    'links_url'         => ['nullable', 'array'],
                    'documents_label'   => ['nullable', 'array'],
                    'documents_url'     => ['nullable', 'array'],
                    // Kalau jam_operasional memang mau diubah dari footer form, biarkan:
                    'jam_operasional'   => ['nullable', 'string', 'max:255'],
                ]);

                // Proses custom links (label/url)
                $customLinks = [];
                $labels = $request->input('links_label', []);
                $urls   = $request->input('links_url', []);

                foreach ($labels as $i => $label) {
                    $label = trim((string)$label);
                    $url   = trim((string)($urls[$i] ?? ''));

                    if ($label !== '' && $url !== '') {
                        $customLinks[] = ['label' => $label, 'url' => $url];
                    }
                }

                $data['custom_links'] = !empty($customLinks)
                    ? json_encode($customLinks, JSON_UNESCAPED_UNICODE)
                    : null;

                $relatedDocuments = [];
                $documentLabels = $request->input('documents_label', []);
                $documentUrls   = $request->input('documents_url', []);

                foreach ($documentLabels as $i => $label) {
                    $label = trim((string) $label);
                    $url   = trim((string) ($documentUrls[$i] ?? ''));

                    if ($label !== '' && $url !== '') {
                        $relatedDocuments[] = ['label' => $label, 'url' => $url];
                    }
                }

                $data['dokumen_terkait'] = !empty($relatedDocuments)
                    ? json_encode($relatedDocuments, JSON_UNESCAPED_UNICODE)
                    : null;

                unset($data['links_label'], $data['links_url'], $data['documents_label'], $data['documents_url']);
                break;

            default:
                return back()->with('error', 'Kategori penyimpanan tidak dikenali.');
        }

        // Ambil data lama untuk hapus logo lama
        $about = AboutSetting::firstOrNew(['kategori_instansi' => $kategori]);

        // Handle Logo Upload
        if ($section === 'branding' && $request->hasFile('logo')) {
            if ($about->exists && $about->logo_path && Storage::disk('public')->exists($about->logo_path)) {
                Storage::disk('public')->delete($about->logo_path);
            }

            $folderName = 'uploads/logo/' . ($kategori ?? 'umum');
            $path = $request->file('logo')->store($folderName, 'public');

            $data['logo_path'] = $path;
            unset($data['logo']);
        }

        // Handle JSON Bidang
        if ($section === 'bidang' && array_key_exists('bidang_options', $data)) {
            $raw = (string)($data['bidang_options'] ?? '');
            $arr = collect(preg_split("/\r\n|\n|\r/", $raw))
                ->map(fn ($x) => trim((string)$x))
                ->filter()
                ->values()
                ->all();

            $data['bidang_options'] = json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // Handle Time Jadwal: dukung "09:00", "09:00:00" bahkan "09.00"
        if ($section === 'jadwal') {
            foreach ($data as $key => $val) {
                $val = str_replace('.', ':', (string)$val); // dukung 09.00
                if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $val)) {
                    $data[$key] = substr($val, 0, 5); // simpan HH:MM
                }
            }
        }

        // Handle Lokasi (GPS gabungan)
        if ($section === 'lokasi') {
            $gps = explode(',', (string)($request->office_gps ?? ''));
            $data['office_latitude']  = trim((string)($gps[0] ?? '')) ?: null;
            $data['office_longitude'] = trim((string)($gps[1] ?? '')) ?: null;
            $data['radius_meter']     = $request->radius_meter;
            $data['office_name']      = $request->office_name;
        }

        AboutSetting::updateOrCreate(
            ['kategori_instansi' => $kategori],
            $data
        );

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}

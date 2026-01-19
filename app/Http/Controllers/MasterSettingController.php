<?php

namespace App\Http\Controllers;

use App\Models\AboutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterSettingController extends Controller
{
    /**
     * Menampilkan Form Edit Global Branding
     */
    public function index()
    {
        // Paksa ambil setting milik 'MASTER'
        $about = AboutSetting::firstOrCreate(['kategori_instansi' => 'MASTER']);

        // Kita reuse view admin saja biar hemat, tapi nanti headernya beda
        return view('master.settings.index', compact('about'));
    }

    /**
     * Update Global Branding
     */
    public function update(Request $request)
    {
        // Target Kategori MASTER
        $kategori = 'MASTER';
        $about = AboutSetting::firstOrCreate(['kategori_instansi' => $kategori]);

        $section = $request->input('section');
        $data = [];

        // Validasi Sederhana (Fokus Branding & Footer)
        if ($section === 'branding') {
            $data = $request->validate([
                'app_name' => 'required|string|max:50',
                'agency_name' => 'required|string|max:50',
                'app_description' => 'nullable|string|max:150',
                'logo' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                if ($about->logo_path && Storage::disk('public')->exists($about->logo_path)) {
                    Storage::disk('public')->delete($about->logo_path);
                }
                $data['logo_path'] = $request->file('logo')->store('uploads/logo/global', 'public');
                unset($data['logo']);
            }
        } elseif ($section === 'footer') {
             $data = $request->validate([
                'office_address' => 'nullable|string',
                'office_phone' => 'nullable|string',
                'office_email' => 'nullable|email',
                'social_instagram' => 'nullable|url',
                // ... validasi sosmed lain ...
            ]);
        }

        $about->update($data);

        return back()->with('success', 'Global Branding berhasil diperbarui.');
    }
}

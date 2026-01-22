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
    $kategori = 'MASTER';
    $about = AboutSetting::firstOrCreate(['kategori_instansi' => $kategori]);

    $section = $request->input('section');
    $data = [];

    /* ===============================
     | 1. BRANDING
     ===============================*/
    if ($section === 'branding') {

        $data = $request->validate([
            'app_name'        => 'required|string|max:50',
            'agency_name'     => 'required|string|max:50',
            'app_description' => 'nullable|string|max:150',
            'running_text'    => 'nullable|string',
            'logo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {

            if ($about->logo_path && Storage::disk('public')->exists($about->logo_path)) {
                Storage::disk('public')->delete($about->logo_path);
            }

            $data['logo_path'] = $request->file('logo')
                ->store('uploads/logo/global', 'public');

            unset($data['logo']);
        }
    }

    /* ===============================
     | 2. WELCOME PAGE (HERO IMAGE)
     ===============================*/
    elseif ($section === 'welcome') {

        $request->validate([
            'hero_image' => 'required|image|max:4096',
        ]);

        if ($request->hasFile('hero_image')) {

            $file = $request->file('hero_image');
            $filename = 'hero_' . time() . '.jpg';
            $dir = storage_path('app/public/hero');

            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $source = imagecreatefromstring(file_get_contents($file));

            $srcWidth  = imagesx($source);
            $srcHeight = imagesy($source);

            $targetWidth  = 1200;
            $targetHeight = 500;

            $srcRatio    = $srcWidth / $srcHeight;
            $targetRatio = $targetWidth / $targetHeight;

            if ($srcRatio > $targetRatio) {
                $newHeight = $srcHeight;
                $newWidth  = $srcHeight * $targetRatio;
                $srcX = ($srcWidth - $newWidth) / 2;
                $srcY = 0;
            } else {
                $newWidth  = $srcWidth;
                $newHeight = $srcWidth / $targetRatio;
                $srcX = 0;
                $srcY = ($srcHeight - $newHeight) / 2;
            }

            $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

            imagecopyresampled(
                $canvas,
                $source,
                0, 0,
                $srcX, $srcY,
                $targetWidth, $targetHeight,
                $newWidth, $newHeight
            );

            imagejpeg($canvas, $dir . '/' . $filename, 85);

            imagedestroy($source);
            imagedestroy($canvas);

            if ($about->hero_image_path) {
                @unlink(storage_path('app/public/' . $about->hero_image_path));
            }

            $about->update([
                'hero_image_path' => 'hero/' . $filename
            ]);
        }

        return back()->with('success', 'Welcome Page berhasil diperbarui.');
    }

    /* ===============================
     | 3. FOOTER
     ===============================*/
    elseif ($section === 'footer') {

        $data = $request->validate([
            'office_address' => 'nullable|string',
            'office_phone'   => 'nullable|string',
            'office_email'   => 'nullable|email',
            'social_instagram' => 'nullable|url',
        ]);
    }

    /* ===============================
     | SIMPAN UMUM
     ===============================*/
    if (!empty($data)) {
        $about->update($data);
    }

    return back()->with('success', 'Pengaturan berhasil diperbarui.');
}

}

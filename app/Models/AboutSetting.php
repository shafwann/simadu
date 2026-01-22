<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AboutSetting extends Model
{
    protected $table = 'about_settings';

    protected $fillable = [
        // key
        'kategori_instansi',

        // identitas
        'nama_instansi',
        'kepala_instansi',
        'nip_kepala_instansi',
        'jam_operasional',

        // lokasi kantor (absensi GPS)
        'office_latitude',
        'office_longitude',
        'radius_meter',
        'office_name',

        // check-in
        'senin_mulai', 'senin_selesai',
        'selasa_mulai', 'selasa_selesai',
        'rabu_mulai', 'rabu_selesai',
        'kamis_mulai', 'kamis_selesai',
        'jumat_mulai', 'jumat_selesai',

        // check-out
        'senin_checkout_mulai', 'senin_checkout_selesai',
        'selasa_checkout_mulai', 'selasa_checkout_selesai',
        'rabu_checkout_mulai', 'rabu_checkout_selesai',
        'kamis_checkout_mulai', 'kamis_checkout_selesai',
        'jumat_checkout_mulai', 'jumat_checkout_selesai',

        // branding
        'app_name',
        'agency_name',
        'app_description',
        'logo_path',

        // bidang (json)
        'bidang_options',

        // footer & kontak
        'office_address',
        'office_phone',
        'office_email',
        'social_instagram',
        'social_facebook',
        'social_youtube',
        'social_x',
        'google_maps_embed',
        'custom_links',
        'whatsapp_number', // kalau kolomnya ada di migration
    ];

    protected $casts = [
        'office_latitude'  => 'float',
        'office_longitude' => 'float',
        'radius_meter'     => 'integer',
    ];

    /**
     * Ambil opsi Bidang/Divisi dari DB (fallback default jika kosong).
     */
    public static function bidangOptions($instansi = null): array
    {
        // jika instansi tidak disebut, ambil dari user login
        if (!$instansi && Auth::check()) {
            $instansi = Auth::user()->kategori_instansi;
        }

        // normalisasi kategori
        $kategori = trim((string)($instansi ?? 'UMUM'));
        if ($kategori === '') $kategori = 'UMUM';

        $about = self::where('kategori_instansi', $kategori)->first();

        $arr = null;
        if ($about && !empty($about->bidang_options)) {
            $arr = json_decode($about->bidang_options, true);
        }

        if (is_array($arr) && count($arr) > 0) {
            return array_values(array_filter(array_map('trim', $arr)));
        }

        // fallback default
        return [
            'Ekonomi',
            'Sekretariat',
            'Sosial & Budaya',
            'Fisik & Prasarana',
            'Penelitian, Pengembangan & Penyusunan Program',
        ];
    }

    /**
     * (OPSIONAL) Helper: ambil jadwal checkin/checkout untuk hari ini.
     * Output: ['checkin_mulai'=>..., 'checkin_selesai'=>..., 'checkout_mulai'=>..., 'checkout_selesai'=>...]
     */
    public function jadwalHariIni(string $timezone = 'Asia/Jakarta'): array
    {
        $day = strtolower(now($timezone)->isoFormat('dddd')); // senin/selasa/...
        // kalau isoFormat locale belum id, pakai Carbon::now()->dayOfWeekIso map manual di controller.

        $map = [
            'monday' => 'senin',
            'tuesday' => 'selasa',
            'wednesday' => 'rabu',
            'thursday' => 'kamis',
            'friday' => 'jumat',
            'senin' => 'senin',
            'selasa' => 'selasa',
            'rabu' => 'rabu',
            'kamis' => 'kamis',
            'jumat' => 'jumat',
        ];

        $d = $map[$day] ?? null;
        if (!$d) {
            return [
                'checkin_mulai' => null,
                'checkin_selesai' => null,
                'checkout_mulai' => null,
                'checkout_selesai' => null,
            ];
        }

        return [
            'checkin_mulai'    => $this->{$d . '_mulai'} ?? null,
            'checkin_selesai'  => $this->{$d . '_selesai'} ?? null,
            'checkout_mulai'   => $this->{$d . '_checkout_mulai'} ?? null,
            'checkout_selesai' => $this->{$d . '_checkout_selesai'} ?? null,
        ];
    }
}

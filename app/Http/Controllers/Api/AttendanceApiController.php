<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSetting;
use App\Models\Attendance;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function today(Request $request): JsonResponse
    {
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->latest()
            ->first();

        return response()->json([
            'data' => $attendance,
            'schedule' => $this->todaySchedule($request),
            'office' => $this->officeConfig($request),
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Attendance::where('user_id', $request->user()->id)
            ->orderByDesc('tanggal');

        if (!empty($validated['year'])) {
            $query->whereYear('tanggal', $validated['year']);
        }

        if (!empty($validated['month'])) {
            $query->whereMonth('tanggal', $validated['month']);
        }

        return response()->json($query->paginate($validated['per_page'] ?? 10));
    }

    public function checkIn(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->status !== 'aktif') {
            return response()->json(['message' => 'Akun Anda tidak aktif, tidak bisa melakukan presensi.'], 403);
        }

        $validated = $request->validate([
            'location' => ['required', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'face_verification_token' => ['required', 'string'],
        ]);

        $faceGate = $this->ensureFaceVerified($request, 'check-in');
        if ($faceGate) {
            return $faceGate;
        }

        $schedule = $this->todaySchedule($request);
        if (isset($schedule['error'])) {
            return response()->json(['message' => $schedule['error']], 422);
        }

        $exists = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah melakukan check-in hari ini.'], 422);
        }

        $now = Carbon::now('Asia/Jakarta');
        $checkinStart = Carbon::createFromTimeString($schedule['checkin_mulai'], 'Asia/Jakarta');
        $checkinEnd = Carbon::createFromTimeString($schedule['checkin_selesai'], 'Asia/Jakarta');

        if ($now->lt($checkinStart)) {
            return response()->json(['message' => "Absensi masuk belum dibuka. Mulai pukul {$checkinStart->format('H:i')} WIB."], 422);
        }

        [$officeLat, $officeLng, $maxRadius] = $this->officeTuple($request);
        [$latStr, $lngStr] = explode(',', $validated['location']);
        $distance = $this->distanceMeters($officeLat, $officeLng, (float) $latStr, (float) $lngStr);

        if ($distance > $maxRadius) {
            return response()->json(['message' => 'Gagal! Jarak Anda ' . round($distance) . 'm dari kantor.'], 422);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'tanggal' => $now->toDateString(),
            'check_in' => $now->toTimeString(),
            'check_out' => null,
            'status' => $now->gt($checkinEnd) ? 'Telat' : 'Hadir',
            'keterangan' => null,
            'location' => $validated['location'],
        ]);

        $this->clearFaceSession();

        return response()->json([
            'message' => 'Check-in berhasil.',
            'data' => $attendance,
        ], 201);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->status !== 'aktif') {
            return response()->json(['message' => 'Akun Anda tidak aktif, tidak bisa melakukan presensi.'], 403);
        }

        $validated = $request->validate([
            'location' => ['required', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'face_verification_token' => ['required', 'string'],
        ]);

        $faceGate = $this->ensureFaceVerified($request, 'check-out');
        if ($faceGate) {
            return $faceGate;
        }

        if (!Logbook::where('user_id', $user->id)->whereDate('tanggal', Carbon::today('Asia/Jakarta'))->exists()) {
            return response()->json(['message' => 'Anda harus mengisi logbook terlebih dahulu sebelum absen pulang.'], 422);
        }

        $schedule = $this->todaySchedule($request);
        if (isset($schedule['error'])) {
            return response()->json(['message' => $schedule['error']], 422);
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', Carbon::today('Asia/Jakarta'))
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['message' => 'Anda belum melakukan check-in hari ini.'], 422);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Anda sudah melakukan check-out hari ini.'], 422);
        }

        $now = Carbon::now('Asia/Jakarta');
        $checkoutStart = Carbon::createFromTimeString($schedule['checkout_mulai'], 'Asia/Jakarta');
        $checkoutEnd = Carbon::createFromTimeString($schedule['checkout_selesai'], 'Asia/Jakarta');

        if ($now->gt($checkoutEnd)) {
            return response()->json(['message' => "Absensi pulang sudah ditutup (maks {$checkoutEnd->format('H:i')} WIB)."], 422);
        }

        [$officeLat, $officeLng, $maxRadius] = $this->officeTuple($request);
        [$latStr, $lngStr] = explode(',', $validated['location']);
        $distance = $this->distanceMeters($officeLat, $officeLng, (float) $latStr, (float) $lngStr);

        if ($distance > $maxRadius) {
            return response()->json(['message' => 'Gagal! Anda masih di luar area kantor (' . round($distance) . 'm).'], 422);
        }

        $attendance->update([
            'check_out' => $now->toTimeString(),
            'keterangan' => $now->lt($checkoutStart) ? 'Pulang Cepat' : $attendance->keterangan,
            'location' => $validated['location'],
        ]);

        $this->clearFaceSession();

        return response()->json([
            'message' => 'Absen pulang berhasil.',
            'data' => $attendance->fresh(),
        ]);
    }

    private function ensureFaceVerified(Request $request, string $action): ?JsonResponse
    {
        $requestToken = $request->input('face_verification_token');
        $sessionToken = session('face_verification_token');
        $verifiedAction = session('face_verified_action');

        if ($verifiedAction !== $action || !$sessionToken || !hash_equals($sessionToken, $requestToken)) {
            return response()->json([
                'message' => 'Silakan verifikasi wajah sebelum ' . $action . '.',
            ], 409);
        }

        return null;
    }

    private function todaySchedule(Request $request): array
    {
        $about = $this->aboutForUser($request);
        if (!$about) {
            return ['error' => 'Jam absensi belum diatur oleh admin untuk instansi Anda.'];
        }

        $dow = Carbon::now('Asia/Jakarta')->dayOfWeekIso;
        if ($dow >= 6) {
            return ['error' => 'Absensi tidak tersedia di hari Sabtu/Minggu.'];
        }

        $map = [
            1 => ['label' => 'Senin', 'checkin_mulai' => $about->senin_mulai, 'checkin_selesai' => $about->senin_selesai, 'checkout_mulai' => $about->senin_checkout_mulai, 'checkout_selesai' => $about->senin_checkout_selesai],
            2 => ['label' => 'Selasa', 'checkin_mulai' => $about->selasa_mulai, 'checkin_selesai' => $about->selasa_selesai, 'checkout_mulai' => $about->selasa_checkout_mulai, 'checkout_selesai' => $about->selasa_checkout_selesai],
            3 => ['label' => 'Rabu', 'checkin_mulai' => $about->rabu_mulai, 'checkin_selesai' => $about->rabu_selesai, 'checkout_mulai' => $about->rabu_checkout_mulai, 'checkout_selesai' => $about->rabu_checkout_selesai],
            4 => ['label' => 'Kamis', 'checkin_mulai' => $about->kamis_mulai, 'checkin_selesai' => $about->kamis_selesai, 'checkout_mulai' => $about->kamis_checkout_mulai, 'checkout_selesai' => $about->kamis_checkout_selesai],
            5 => ['label' => 'Jumat', 'checkin_mulai' => $about->jumat_mulai, 'checkin_selesai' => $about->jumat_selesai, 'checkout_mulai' => $about->jumat_checkout_mulai, 'checkout_selesai' => $about->jumat_checkout_selesai],
        ];

        $today = $map[$dow] ?? null;
        if (!$today || in_array(null, $today, true) || in_array('', $today, true)) {
            return ['error' => 'Jam check-in/check-out hari ini belum diatur oleh admin.'];
        }

        return $today;
    }

    private function officeConfig(Request $request): array
    {
        [$latitude, $longitude, $radius] = $this->officeTuple($request);

        return compact('latitude', 'longitude', 'radius');
    }

    private function officeTuple(Request $request): array
    {
        $about = $this->aboutForUser($request);

        return [
            (float) ($about?->office_latitude ?? -7.594917597849972),
            (float) ($about?->office_longitude ?? 110.94065517764916),
            (int) ($about?->radius_meter ?? 1500),
        ];
    }

    private function aboutForUser(Request $request): ?AboutSetting
    {
        return AboutSetting::where('kategori_instansi', $request->user()->kategori_instansi ?? 'UMUM')->first();
    }

    private function distanceMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $angle = 2 * asin(sqrt(
            pow(sin(($latTo - $latFrom) / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin(($lonTo - $lonFrom) / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    private function clearFaceSession(): void
    {
        session()->forget('face_verified_action');
        session()->forget('face_intended_action');
        session()->forget('face_pending_location');
        session()->forget('face_verification_token');
    }
}

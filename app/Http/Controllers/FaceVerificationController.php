<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Logbook;
use Carbon\Carbon;

class FaceVerificationController extends Controller
{
    private const FACE_DISTANCE_THRESHOLD = 0.45;

    private function normalizeDescriptor(mixed $descriptor): ?array
    {
        if (is_array($descriptor)) {
            return $descriptor;
        }

        if (!is_string($descriptor)) {
            return null;
        }

        $decoded = json_decode($descriptor, true);

        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        if (!is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    public function setupView()
    {
        if (auth()->user()->face_descriptor) {
            return redirect()->route('attendance.index');
        }
        return view('face.setup');
    }

    public function storeSetup(Request $request)
    {
        $request->validate([
            'face_descriptor' => 'required|json',
            'face_image_base64' => 'required|string',
        ]);

        $imageData = $request->input('face_image_base64');
        if (!str_starts_with($imageData, 'data:image/')) {
            return back()->with('error', 'Format gambar wajah tidak valid.');
        }

        [, $payload] = explode(',', $imageData, 2);
        $binary = base64_decode($payload, true);
        if ($binary === false) {
            return back()->with('error', 'Gagal memproses gambar wajah.');
        }

        $fileName = 'faces/' . auth()->id() . '_' . now()->format('Ymd_His') . '.jpg';
        Storage::disk('public')->put($fileName, $binary);

        // Decode descriptor from JSON string to array before storing
        // (Model will auto-encode when saving due to 'array' cast)
        $descriptor = json_decode($request->face_descriptor, true);

        if (!is_array($descriptor) || count($descriptor) !== 128) {
            return back()->with('error', 'Descriptor harus array 128-dimensional.');
        }

        auth()->user()->update([
            'face_descriptor' => $descriptor,  // Store array, model will JSON-encode it
            'face_image_path' => $fileName,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Face setup completed');
    }

    public function verifyView()
    {
        if (!auth()->user()->face_descriptor) {
            return redirect()->route('face.setup')->with('error', 'Please complete face setup first');
        }

        $intendedAction = session('face_intended_action');
        $pendingLocation = session('face_pending_location');

        // Validasi logbook sebelum checkout
        if ($intendedAction === 'check-out') {
            $hasLogbookToday = Logbook::where('user_id', auth()->id())
                ->whereDate('tanggal', Carbon::today())
                ->exists();

            if (!$hasLogbookToday) {
                session(['pending_checkout' => true]);
                return redirect()->route('logbook.index')
                    ->with('warning', 'Anda harus mengisi logbook terlebih dahulu sebelum absen pulang.');
            }

            if (empty($pendingLocation)) {
                return redirect()->route('attendance.index')
                    ->with('warning', 'Lokasi checkout tidak ditemukan. Silakan klik absen pulang lagi.');
            }
        }

        $actionRoute = match ($intendedAction) {
            'check-in' => route('attendance.checkin'),
            'check-out' => route('attendance.checkout'),
            default => null,
        };

        Log::info('Face verify view opened', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'session_intended_action' => $intendedAction,
            'has_pending_location' => !empty($pendingLocation),
            'user_agent' => (string) request()->userAgent(),
            'referer' => request()->headers->get('referer'),
        ]);

        return view('face.verify', compact('intendedAction', 'pendingLocation', 'actionRoute'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|json',
            'intended_action' => 'nullable|in:check-in,check-out',
        ]);

        $storedDescriptor = $this->normalizeDescriptor(auth()->user()->face_descriptor);
        $currentDescriptor = $this->normalizeDescriptor($request->descriptor);

        if (!is_array($storedDescriptor) || !is_array($currentDescriptor)) {
            return response()->json(['match' => false, 'similarity' => 0, 'message' => 'Descriptor tidak valid'], 422);
        }

        if (count($storedDescriptor) !== 128 || count($currentDescriptor) !== 128) {
            return response()->json(['match' => false, 'similarity' => 0, 'message' => 'Descriptor harus 128-dimensional'], 422);
        }

        $distance = $this->calculateDistance($storedDescriptor, $currentDescriptor);
        $similarity = max(0, min(1, 1 - $distance));
        $match = $distance <= self::FACE_DISTANCE_THRESHOLD;
        $sessionIntendedAction = session('face_intended_action');

        if ($match) {
            $intendedAction = $sessionIntendedAction ?: $request->input('intended_action');

            if (!$intendedAction) {
                Log::warning('Face verify matched but action missing', [
                    'user_id' => auth()->id(),
                    'session_id' => session()->getId(),
                    'session_intended_action' => $sessionIntendedAction,
                    'request_intended_action' => $request->input('intended_action'),
                    'similarity' => round($similarity, 4),
                    'distance' => round($distance, 4),
                    'user_agent' => (string) $request->userAgent(),
                    'origin' => $request->headers->get('origin'),
                    'referer' => $request->headers->get('referer'),
                ]);

                return response()->json([
                    'match' => false,
                    'similarity' => $similarity,
                    'distance' => $distance,
                    'message' => 'Aksi absensi tidak ditemukan',
                ], 409);
            }

            $verificationToken = Str::random(40);
            session([
                'face_verified_action' => $intendedAction,
                'face_verification_token' => $verificationToken,
            ]);

            Log::info('Face verify matched', [
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'session_intended_action' => $sessionIntendedAction,
                'request_intended_action' => $request->input('intended_action'),
                'resolved_intended_action' => $intendedAction,
                'similarity' => round($similarity, 4),
                'distance' => round($distance, 4),
                'user_agent' => (string) $request->userAgent(),
                'origin' => $request->headers->get('origin'),
                'referer' => $request->headers->get('referer'),
            ]);

            return response()->json([
                'match' => true,
                'similarity' => $similarity,
                'distance' => $distance,
                'verification_token' => $verificationToken,
            ]);
        }

        Log::info('Face verify mismatch', [
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'session_intended_action' => $sessionIntendedAction,
            'request_intended_action' => $request->input('intended_action'),
            'similarity' => round($similarity, 4),
            'distance' => round($distance, 4),
            'user_agent' => (string) $request->userAgent(),
        ]);

        return response()->json(['match' => $match, 'similarity' => $similarity, 'distance' => $distance]);
    }

    private function calculateDistance(array $desc1, array $desc2): float
    {
        $sum = 0.0;
        foreach ($desc1 as $i => $v1) {
            $v2 = $desc2[$i] ?? 0;
            $diff = ((float) $v1) - ((float) $v2);
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }
}

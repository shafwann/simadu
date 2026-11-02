<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Models\Logbook;
use Carbon\Carbon;

class FaceVerificationController extends Controller
{
    private const FACE_DISTANCE_THRESHOLD = 0.45;
    private const LIVENESS_TTL_SECONDS = 30;

    private function newLivenessChallenge(): array
    {
        $actions = collect(['left', 'right', 'mouth'])->shuffle()->take(2)->values()->all();
        $challenge = [
            'nonce' => Str::random(48),
            'actions' => $actions,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addSeconds(self::LIVENESS_TTL_SECONDS)->timestamp,
        ];
        session(['face_liveness_challenge' => $challenge]);

        return $challenge;
    }

    private function runPresentationAttackDetection(array $frames): array
    {
        $python = (string) config('pad.python');
        $repository = (string) config('pad.repository');
        $script = base_path('scripts/silent_face_pad_infer.py');

        if ($repository === '' || !is_dir($repository) || !is_file($script)) {
            throw new \RuntimeException('Konfigurasi PAD belum lengkap. Periksa PAD_REPOSITORY dan file model Silent-Face.');
        }

        $process = new Process([$python, $script]);
        $process->setInput(json_encode([
            'repository' => $repository,
            'frames' => $frames,
            'real_threshold' => config('pad.real_threshold'),
            'required_real_frames' => config('pad.required_real_frames'),
        ], JSON_THROW_ON_ERROR));
        // Seed Python from PHP's system RNG for web-server environments where Python cannot read OS entropy.
        $pythonDirectory = dirname($python);
        $condaRoot = dirname(dirname($pythonDirectory));
        $processPath = implode(PATH_SEPARATOR, array_filter([
            $pythonDirectory,
            $pythonDirectory . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'bin',
            $pythonDirectory . DIRECTORY_SEPARATOR . 'Scripts',
            $condaRoot . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'bin',
            (getenv('SystemRoot') ?: 'C:\\Windows') . DIRECTORY_SEPARATOR . 'System32',
            getenv('PATH') ?: '',
        ]));
        $process->setEnv([
            'PATH' => $processPath,
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
            'WINDIR' => getenv('WINDIR') ?: (getenv('SystemRoot') ?: 'C:\\Windows'),
            'TEMP' => getenv('TEMP') ?: sys_get_temp_dir(),
            'TMP' => getenv('TMP') ?: sys_get_temp_dir(),
            'PYTHONHASHSEED' => (string) random_int(1, 4294967295),
            'PYTHONNOUSERSITE' => '1',
        ]);
        $process->setTimeout(30);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(trim($process->getErrorOutput()) ?: 'Proses model PAD gagal dijalankan.');
        }

        $result = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($result['passed'], $result['frames']) || !is_bool($result['passed'])) {
            throw new \RuntimeException('Format hasil model PAD tidak valid.');
        }

        return $result;
    }

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

        $livenessChallenge = $this->newLivenessChallenge();

        return view('face.verify', compact('intendedAction', 'pendingLocation', 'actionRoute', 'livenessChallenge'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'descriptor' => 'required|json',
            'challenge_nonce' => 'required|string',
            'telemetry' => 'required|array|min:4|max:400',
            'telemetry.*.t' => 'required|integer|min:0',
            'telemetry.*.yaw' => 'required|numeric|between:-2,2',
            'telemetry.*.mouth' => 'required|numeric|between:0,2',
            'pad_frames' => 'required|array|size:3',
            'pad_frames.*' => ['required', 'string', 'max:400000', 'regex:/\Adata:image\/jpeg;base64,[A-Za-z0-9+\/=]+\z/'],
            'intended_action' => 'nullable|in:check-in,check-out',
        ]);

        $challenge = session('face_liveness_challenge');
        if (!$challenge || !hash_equals($challenge['nonce'], $request->input('challenge_nonce'))
            || now()->timestamp > $challenge['expires_at']) {
            return response()->json(['match' => false, 'message' => 'Tantangan liveness kedaluwarsa. Muat ulang halaman dan coba lagi.'], 409);
        }

        $telemetry = $request->input('telemetry');
        $duration = end($telemetry)['t'] - $telemetry[0]['t'];
        if ($duration < 1500 || $duration > self::LIVENESS_TTL_SECONDS * 1000) {
            return response()->json(['match' => false, 'message' => 'Durasi tantangan tidak valid. Silakan coba lagi.'], 422);
        }

        $lastTime = -1;
        foreach ($telemetry as $sample) {
            if ($sample['t'] <= $lastTime) {
                return response()->json(['match' => false, 'message' => 'Urutan telemetri tidak valid.'], 422);
            }
            $lastTime = $sample['t'];
        }

        $cursor = 0;
        foreach ($challenge['actions'] as $action) {
            $found = false;
            for (; $cursor < count($telemetry); $cursor++) {
                $sample = $telemetry[$cursor];
                $passed = match ($action) {
                    'left' => $sample['yaw'] <= -0.08,
                    'right' => $sample['yaw'] >= 0.08,
                    'mouth' => $sample['mouth'] >= 0.24,
                };
                if ($passed) {
                    $found = true;
                    $cursor++;
                    break;
                }
            }
            if (!$found) {
                return response()->json(['match' => false, 'message' => 'Tantangan belum terdeteksi sesuai urutan. Muat ulang halaman untuk tantangan baru.'], 422);
            }
        }

        $storedDescriptor = $this->normalizeDescriptor(auth()->user()->face_descriptor);
        $currentDescriptor = $this->normalizeDescriptor($request->descriptor);

        if (!is_array($storedDescriptor) || !is_array($currentDescriptor)) {
            return response()->json(['match' => false, 'similarity' => 0, 'message' => 'Descriptor tidak valid'], 422);
        }

        if (count($storedDescriptor) !== 128 || count($currentDescriptor) !== 128) {
            return response()->json(['match' => false, 'similarity' => 0, 'message' => 'Descriptor harus 128-dimensional'], 422);
        }

        try {
            $padResult = $this->runPresentationAttackDetection($request->input('pad_frames'));
        } catch (\Throwable $e) {
            Log::error('Face PAD inference failed', [
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'match' => false,
                'message' => 'Pemeriksaan anti-spoofing gagal dijalankan. Periksa konfigurasi PAD di server.',
            ], 503);
        }

        if (!$padResult['passed']) {
            Log::warning('Face PAD rejected presentation', [
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'real_frames' => $padResult['real_frames'] ?? null,
                'frame_results' => $padResult['frames'],
            ]);

            return response()->json([
                'match' => false,
                'pad' => $padResult,
                'message' => 'PAD mendeteksi kemungkinan foto atau video replay. Gunakan wajah langsung di depan kamera dan coba lagi.',
            ], 422);
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
            session()->forget('face_liveness_challenge');
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
                'pad' => $padResult,
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

<x-app-layout>
    <style>
        #webcam {
            transform: scaleX(-1);
        }

        #overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            transform: scaleX(-1);
            pointer-events: none;
        }

        .cam-loading-hide {
            opacity: 0;
            pointer-events: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            animation: spin 0.8s linear infinite;
        }
    </style>

    <div class="py-12 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('warning'))
            <div class="p-4 bg-yellow-50 text-yellow-800 rounded-xl border border-yellow-100 text-center">
                {{ session('warning') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-2xl border shadow-sm space-y-4">
            <h2 class="text-xl font-bold text-gray-800 text-center">Face Verification Sebelum Absen</h2>
            <p class="text-sm text-gray-500 text-center">Verifikasi wajah diperlukan sebelum membuka halaman absensi.</p>

            <div class="relative rounded-xl overflow-hidden border bg-slate-900 aspect-[4/3]">
                <video id="webcam" autoplay muted playsinline class="w-full h-full object-cover block"></video>
                <canvas id="overlay"></canvas>
                <div id="camLoading"
                    class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center gap-3 z-10 transition-opacity duration-300">
                    <div class="spinner w-7 h-7 rounded-full border-2 border-white/20 border-t-blue-500"></div>
                    <p id="camLoadingText" class="text-xs text-slate-400">Memuat model AI...</p>
                </div>
            </div>

            <div id="statusBox" class="text-sm rounded-xl border px-3 py-2 bg-gray-50 text-gray-700 border-gray-200">
                Mempersiapkan kamera...
            </div>

            <!-- Confidence Bar -->
            <div class="space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-600">Kemiripan Wajah</span>
                    <span id="confidencePercent" class="font-semibold text-gray-800">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="confidenceBar"
                        class="bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 h-full transition-all duration-300"
                        style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500">Posisikan wajah Anda. Sistem akan memverifikasi otomatis jika kecocokan
                    stabil.
                </p>
            </div>

            <form id="verifiedAttendanceForm" method="POST" action="{{ $actionRoute ?? '' }}" class="space-y-3">
                @csrf
                <input type="hidden" name="location" id="verifiedLocationInput" value="{{ $pendingLocation ?? '' }}">
                <input type="hidden" name="face_verification_token" id="verifiedTokenInput" value="">

                <div id="attendanceReadyBox">
                    <button type="submit" id="verifiedAttendanceSubmit" disabled
                        class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white transition disabled:cursor-not-allowed disabled:bg-gray-300">
                        Absen Sekarang
                    </button>
                </div>
            </form>

            <div class="flex justify-center gap-3 text-sm">
                <a href="{{ route('face.setup') }}" class="text-blue-600 hover:underline">Setup Ulang Wajah</a>
                <a href="{{ route('face-reset-request.create') }}" class="text-gray-600 hover:underline">Ajukan Reset
                    Wajah</a>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const waitForFaceApi = setInterval(() => {
                if (typeof faceapi !== 'undefined') {
                    clearInterval(waitForFaceApi);
                    initFaceVerify();
                }
            }, 100);
        });

        async function initFaceVerify() {
            const video = document.getElementById('webcam');
            const overlayEl = document.getElementById('overlay');
            const camLoading = document.getElementById('camLoading');
            const camLoadingText = document.getElementById('camLoadingText');
            const statusBox = document.getElementById('statusBox');
            const confidenceBar = document.getElementById('confidenceBar');
            const confidencePercent = document.getElementById('confidencePercent');
            const verifiedAttendanceForm = document.getElementById('verifiedAttendanceForm');
            const verifiedLocationInput = document.getElementById('verifiedLocationInput');
            const verifiedTokenInput = document.getElementById('verifiedTokenInput');
            const attendanceReadyBox = document.getElementById('attendanceReadyBox');
            const verifiedAttendanceSubmit = document.getElementById('verifiedAttendanceSubmit');
            const actionRoute = @json($actionRoute);
            const pendingLocation = @json($pendingLocation);
            const intendedAction = @json($intendedAction);

            const setStatus = (text, tone = 'neutral') => {
                const toneClass = {
                    neutral: 'bg-gray-50 text-gray-700 border-gray-200',
                    info: 'bg-blue-50 text-blue-700 border-blue-200',
                    success: 'bg-green-50 text-green-700 border-green-200',
                    error: 'bg-red-50 text-red-700 border-red-200'
                };
                statusBox.className = 'text-sm rounded-xl border px-3 py-2 ' + (toneClass[tone] || toneClass
                    .neutral);
                statusBox.textContent = text;
            };

            try {
                setStatus('Memuat model AI...', 'info');
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models')
                ]);

                camLoadingText.textContent = 'Mengakses kamera...';
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: 640,
                        height: 480,
                        facingMode: 'user'
                    },
                    audio: false
                });

                video.srcObject = stream;
                await new Promise(resolve => video.onloadedmetadata = resolve);

                overlayEl.width = video.videoWidth;
                overlayEl.height = video.videoHeight;

                camLoading.classList.add('cam-loading-hide');
                setStatus('Kamera siap. Posisikan wajah Anda...', 'info');

                // Mulai auto-detection
                startAutoDetection(video, overlayEl, setStatus, confidenceBar, confidencePercent,
                    verifiedAttendanceForm, verifiedLocationInput, verifiedTokenInput, pendingLocation,
                    attendanceReadyBox, verifiedAttendanceSubmit, intendedAction);
            } catch (e) {
                setStatus('Gagal menyiapkan kamera/model: ' + e.message, 'error');
                camLoadingText.textContent = 'Gagal menyiapkan verifikasi';
                return;
            }
        }

        async function startAutoDetection(video, overlayEl, setStatus, confidenceBar, confidencePercent,
            verifiedAttendanceForm, verifiedLocationInput, verifiedTokenInput, pendingLocation,
            attendanceReadyBox, verifiedAttendanceSubmit, intendedAction) {
            const options = new faceapi.TinyFaceDetectorOptions({
                inputSize: 224,
                scoreThreshold: 0.5
            });

            // Parse stored descriptor — handle both JSON string and already-parsed object
            let storedDescriptor;
            try {
                let rawDescriptor = @json(auth()->user()->face_descriptor);
                console.log('=== DESCRIPTOR DEBUG ===');
                console.log('Raw type:', typeof rawDescriptor);
                console.log('Raw value (first 100 chars):', String(rawDescriptor).substring(0, 100));

                if (rawDescriptor === null || rawDescriptor === undefined) {
                    throw new Error('Descriptor not set. Please setup face first.');
                }

                // Handle double-encoded JSON (e.g. "\"[...array...]\"")
                if (typeof rawDescriptor === 'string' && rawDescriptor.startsWith('"') && rawDescriptor.endsWith('"')) {
                    console.log('Detected double-encoded string, decoding once more...');
                    rawDescriptor = JSON.parse(rawDescriptor);
                }

                // Now parse to array if still string
                if (typeof rawDescriptor === 'string') {
                    console.log('Parsing JSON string to array...');
                    storedDescriptor = JSON.parse(rawDescriptor);
                } else if (Array.isArray(rawDescriptor)) {
                    console.log('Already array, using directly');
                    storedDescriptor = rawDescriptor;
                } else if (typeof rawDescriptor === 'object' && rawDescriptor !== null) {
                    console.log('Object type, converting to array...');
                    storedDescriptor = Object.values(rawDescriptor);
                } else {
                    throw new Error('Invalid descriptor format: ' + typeof rawDescriptor);
                }

                console.log('After parse - type:', typeof storedDescriptor, 'is array:', Array.isArray(
                    storedDescriptor));
                console.log('Descriptor length:', storedDescriptor?.length);
                console.log('First 5 values:', storedDescriptor?.slice(0, 5));

                if (!Array.isArray(storedDescriptor) || storedDescriptor.length !== 128) {
                    throw new Error(
                        `Descriptor must be 128-dimensional array, got ${Array.isArray(storedDescriptor) ? storedDescriptor.length : 'not-array'} ` +
                        typeof storedDescriptor);
                }

                // Verify all values are numbers
                const allNumbers = storedDescriptor.every(v => typeof v === 'number');
                if (!allNumbers) {
                    const badIndex = storedDescriptor.findIndex(v => typeof v !== 'number');
                    throw new Error(`Descriptor[${badIndex}] is not a number: ${typeof storedDescriptor[badIndex]}`);
                }

                console.log('✓ Descriptor valid!');
            } catch (e) {
                setStatus('Error: Descriptor tidak valid - ' + e.message + '. Silakan re-setup wajah.', 'error');
                console.error('DESCRIPTOR ERROR:', e);
                return;
            }

            const ctx = overlayEl.getContext('2d');

            let stableFrameCount = 0;
            const DISTANCE_THRESHOLD = 0.45;
            const STABLE_FRAMES_NEEDED = 4;
            let isVerifying = false;
            let detectionFrameCount = 0;

            async function detectionLoop() {
                if (isVerifying) return;

                detectionFrameCount++;

                try {
                    const detections = await faceapi
                        .detectSingleFace(video, options)
                        .withFaceLandmarks(true)
                        .withFaceDescriptor();

                    ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);

                    if (!detections) {
                        if (detectionFrameCount % 30 === 0) {
                            console.log('Frame', detectionFrameCount, ': No face detected');
                        }
                        stableFrameCount = 0;
                        confidencePercent.textContent = '0%';
                        confidenceBar.style.width = '0%';
                        setStatus('Wajah tidak terdeteksi. Posisikan wajah ke kamera.', 'neutral');
                        requestAnimationFrame(detectionLoop);
                        return;
                    }

                    const currentDescriptor = Array.from(detections.descriptor);
                    const distance = calculateDistanceJS(storedDescriptor, currentDescriptor);
                    const confidencePercVal = Math.round(Math.max(0, Math.min((1 - distance) * 100, 100)));
                    confidencePercent.textContent = confidencePercVal + '%';
                    confidenceBar.style.width = confidencePercVal + '%';

                    // Draw detection box
                    const box = detections.detection.box;
                    drawCornerBox(ctx, box, distance <= DISTANCE_THRESHOLD);

                    if (detectionFrameCount % 10 === 0) {
                        console.log('Frame', detectionFrameCount, ': Distance =', distance.toFixed(3),
                            'Stable frames:', stableFrameCount);
                    }

                    if (distance <= DISTANCE_THRESHOLD) {
                        stableFrameCount++;
                        const framesLeft = STABLE_FRAMES_NEEDED - stableFrameCount;
                        setStatus(
                            `✓ Wajah cocok! Akurasi ${confidencePercVal}% - Verifikasi dalam ${Math.max(0, framesLeft)} frame...`,
                            'success');

                        if (stableFrameCount >= STABLE_FRAMES_NEEDED && !isVerifying) {
                            isVerifying = true;
                            console.log('✓ VERIFICATION TRIGGERED at frame', detectionFrameCount);
                            setStatus('✓ Verifikasi berhasil! Mengirim data...', 'success');
                            await submitVerification(detections, pendingLocation, verifiedAttendanceForm,
                                verifiedLocationInput, verifiedTokenInput, attendanceReadyBox,
                                verifiedAttendanceSubmit, intendedAction);
                            return;
                        }
                    } else {
                        stableFrameCount = 0;
                        setStatus(`Akurasi ${confidencePercVal}% - Wajah belum cocok`, 'neutral');
                    }

                } catch (e) {
                    console.error('Detection error at frame', detectionFrameCount, ':', e);
                }

                requestAnimationFrame(detectionLoop);
            }

            console.log('Starting detection loop with stored descriptor:', storedDescriptor.length, 'dimensions');
            detectionLoop();
        }

        function calculateDistanceJS(desc1, desc2) {
            const sum = desc1.reduce((acc, a, i) => {
                const diff = a - (desc2[i] || 0);
                return acc + (diff * diff);
            }, 0);
            return Math.sqrt(sum);
        }

        function drawCornerBox(ctx, box, isMatch) {
            const {
                x,
                y,
                width: w,
                height: h
            } = box;
            const len = 14;
            ctx.strokeStyle = isMatch ? '#22c55e' : '#3b82f6';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(x, y + len);
            ctx.lineTo(x, y);
            ctx.lineTo(x + len, y);
            ctx.moveTo(x + w - len, y);
            ctx.lineTo(x + w, y);
            ctx.lineTo(x + w, y + len);
            ctx.moveTo(x, y + h - len);
            ctx.lineTo(x, y + h);
            ctx.lineTo(x + len, y + h);
            ctx.moveTo(x + w - len, y + h);
            ctx.lineTo(x + w, y + h);
            ctx.lineTo(x + w, y + h - len);
            ctx.stroke();
        }

        async function submitVerification(detections, pendingLocation, verifiedAttendanceForm, verifiedLocationInput,
            verifiedTokenInput, attendanceReadyBox, verifiedAttendanceSubmit, intendedAction, retryCount = 0) {
            const descriptor = Array.from(detections.descriptor);
            const statusBox = document.getElementById('statusBox');

            try {
                const res = await fetch('{{ route('face.verify.submit') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        descriptor: JSON.stringify(descriptor),
                        intended_action: intendedAction
                    })
                });

                if (!res.ok) {
                    let errorJson = null;
                    let message = 'HTTP ' + res.status;
                    try {
                        errorJson = await res.json();
                        if (errorJson?.message) {
                            message = errorJson.message;
                        }
                    } catch (parseError) {
                        console.warn('Failed to parse verification error response', parseError);
                    }

                    if (res.status === 409 && retryCount < 1) {
                        console.warn('Verification got 409, retrying once with explicit action context');
                        statusBox.className =
                            'text-sm rounded-xl border px-3 py-2 bg-yellow-50 text-yellow-800 border-yellow-200';
                        statusBox.textContent = 'Sesi verifikasi sedang diselaraskan. Mencoba ulang sekali...';
                        await new Promise(resolve => setTimeout(resolve, 600));
                        return submitVerification(
                            detections,
                            pendingLocation,
                            verifiedAttendanceForm,
                            verifiedLocationInput,
                            verifiedTokenInput,
                            attendanceReadyBox,
                            verifiedAttendanceSubmit,
                            intendedAction,
                            retryCount + 1
                        );
                    }

                    if (res.status === 409) {
                        message =
                            'Sesi verifikasi iPhone/Safari tidak sinkron. Silakan kembali ke halaman absensi lalu coba lagi.';
                    } else if (res.status === 422) {
                        message = errorJson?.message || 'Data verifikasi wajah tidak valid. Silakan setup ulang wajah.';
                    }

                    throw new Error(message);
                }

                const json = await res.json();
                if (json.match && json.verification_token) {
                    verifiedLocationInput.value = pendingLocation;
                    verifiedTokenInput.value = json.verification_token;

                    if (attendanceReadyBox) {
                        attendanceReadyBox.classList.remove('hidden');
                    }

                    if (verifiedAttendanceSubmit) {
                        verifiedAttendanceSubmit.disabled = false;
                    }

                    statusBox.className =
                        'text-sm rounded-xl border px-3 py-2 bg-green-50 text-green-700 border-green-200';
                    statusBox.textContent = 'Verifikasi berhasil. Silakan klik tombol absen.';
                    setTimeout(() => {
                        statusBox.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 200);
                } else {
                    statusBox.className =
                        'text-sm rounded-xl border px-3 py-2 bg-red-50 text-red-700 border-red-200';
                    statusBox.textContent = 'Verifikasi gagal. Wajah belum cocok. Coba lagi dengan posisi lebih lurus.';
                    setTimeout(() => location.reload(), 2000);
                }
            } catch (e) {
                console.error('Verification error:', e);
                statusBox.className =
                    'text-sm rounded-xl border px-3 py-2 bg-red-50 text-red-700 border-red-200';
                statusBox.textContent = 'Error verifikasi: ' + e.message;
                setTimeout(() => location.reload(), 3500);
            }
        }
    </script>
</x-app-layout>

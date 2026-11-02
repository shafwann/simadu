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
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 text-sm text-blue-900">
                <p class="font-semibold">Tantangan liveness</p>
                <p id="livenessPrompt" class="mt-1">Menyiapkan instruksi acak...</p>
                <p class="mt-1 text-xs text-blue-700">Selesaikan sesuai urutan dalam <strong id="livenessCountdown">30</strong> detik.</p>
            </div>

            <button type="button" id="cameraRetryBtn"
                class="hidden w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                Mulai Kamera
            </button>

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

    <script defer src="{{ asset('vendor/face-api.min.js') }}"
        onerror="window.faceApiLoadFailed = true"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cameraRetryBtn = document.getElementById('cameraRetryBtn');
            const statusBox = document.getElementById('statusBox');
            let started = false;
            window.livenessDeadlineAt = performance.now() + 30000;
            const countdown = document.getElementById('livenessCountdown');
            const countdownTimer = setInterval(() => {
                const remaining = Math.max(0, Math.ceil((window.livenessDeadlineAt - performance.now()) / 1000));
                countdown.textContent = remaining;
                if (remaining === 0) clearInterval(countdownTimer);
            }, 200);

            function startOnce() {
                if (started) return;
                started = true;
                cameraRetryBtn.classList.add('hidden');
                initFaceVerify().finally(() => {
                    if (!cameraRetryBtn.classList.contains('hidden')) {
                        started = false;
                    }
                });
            }

            cameraRetryBtn.addEventListener('click', startOnce);

            const waitForFaceApi = setInterval(() => {
                if (window.faceApiLoadFailed) {
                    clearInterval(waitForFaceApi);
                    statusBox.className = 'text-sm rounded-xl border px-3 py-2 bg-red-50 text-red-700 border-red-200';
                    statusBox.textContent = 'Gagal memuat face-api.js lokal. Pastikan file public/vendor/face-api.min.js ikut terdeploy.';
                    return;
                }

                if (typeof faceapi !== 'undefined') {
                    clearInterval(waitForFaceApi);
                    startOnce();
                }
            }, 100);

            setTimeout(() => {
                if (typeof faceapi === 'undefined' && !window.faceApiLoadFailed) {
                    clearInterval(waitForFaceApi);
                    statusBox.className = 'text-sm rounded-xl border px-3 py-2 bg-red-50 text-red-700 border-red-200';
                    statusBox.textContent = 'face-api.js belum termuat. Pastikan file public/vendor/face-api.min.js bisa diakses.';
                }
            }, 15000);
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
            const cameraRetryBtn = document.getElementById('cameraRetryBtn');
            const actionRoute = @json($actionRoute);
            const pendingLocation = @json($pendingLocation);
            const intendedAction = @json($intendedAction);
            const livenessChallenge = @json($livenessChallenge);
            const modelBaseUri = @json(asset('models'));

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
                if (!window.isSecureContext) {
                    throw new Error('Safari iOS hanya mengizinkan kamera pada HTTPS. Buka situs deploy dengan https://.');
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser tidak mendukung akses kamera. Gunakan Safari iOS terbaru dan izinkan kamera untuk situs ini.');
                }

                setStatus('Memuat model AI...', 'info');
                if (!window.faceModelsReady) {
                    window.faceModelsReady = (async () => {
                        await loadFaceModelWithRetry(faceapi.nets.tinyFaceDetector, modelBaseUri, 'deteksi wajah');
                        await loadFaceModelWithRetry(faceapi.nets.faceLandmark68TinyNet, modelBaseUri, 'landmark');
                        await loadFaceModelWithRetry(faceapi.nets.faceRecognitionNet, modelBaseUri, 'pengenalan wajah');
                    })();
                }
                try {
                    await window.faceModelsReady;
                } catch (modelError) {
                    window.faceModelsReady = null;
                    throw modelError;
                }

                camLoadingText.textContent = 'Mengakses kamera...';
                const stream = await openUserCamera();

                video.setAttribute('playsinline', '');
                video.setAttribute('webkit-playsinline', '');
                video.muted = true;
                video.autoplay = true;
                video.srcObject = stream;
                await new Promise(resolve => video.onloadedmetadata = resolve);
                await video.play();

                overlayEl.width = video.videoWidth;
                overlayEl.height = video.videoHeight;

                camLoading.classList.add('cam-loading-hide');
                setStatus('Kamera siap. Posisikan wajah Anda...', 'info');

                // Mulai auto-detection
                startAutoDetection(video, overlayEl, setStatus, confidenceBar, confidencePercent,
                    verifiedAttendanceForm, verifiedLocationInput, verifiedTokenInput, pendingLocation,
                    attendanceReadyBox, verifiedAttendanceSubmit, intendedAction, livenessChallenge);
            } catch (e) {
                setStatus('Gagal menyiapkan verifikasi: ' + e.message, 'error');
                camLoadingText.textContent = 'Gagal menyiapkan verifikasi';
                cameraRetryBtn.classList.remove('hidden');
                return;
            }
        }

        async function loadFaceModelWithRetry(net, modelBaseUri, label) {
            if (net.isLoaded) return;

            let lastError;
            for (let attempt = 1; attempt <= 3; attempt++) {
                try {
                    await net.loadFromUri(modelBaseUri);
                    return;
                } catch (error) {
                    lastError = error;
                    if (attempt < 3) await new Promise(resolve => setTimeout(resolve, attempt * 500));
                }
            }

            throw new Error(`Gagal memuat model ${label} setelah 3 percobaan: ${lastError?.message || 'Failed to fetch'}`);
        }

        async function openUserCamera() {
            const preferredConstraints = {
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: { ideal: 'user' }
                },
                audio: false
            };

            try {
                return await navigator.mediaDevices.getUserMedia(preferredConstraints);
            } catch (error) {
                if (error && (error.name === 'OverconstrainedError' || error.name === 'ConstraintNotSatisfiedError')) {
                    return navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                }
                throw error;
            }
        }

        async function startAutoDetection(video, overlayEl, setStatus, confidenceBar, confidencePercent,
            verifiedAttendanceForm, verifiedLocationInput, verifiedTokenInput, pendingLocation,
            attendanceReadyBox, verifiedAttendanceSubmit, intendedAction, livenessChallenge) {
            const options = new faceapi.TinyFaceDetectorOptions({
                inputSize: 224,
                scoreThreshold: 0.5
            });

            const ctx = overlayEl.getContext('2d');

            const actionLabels = { left: 'Tengok ke kiri', right: 'Tengok ke kanan', mouth: 'Buka mulut' };
            const telemetry = [];
            const prompt = document.getElementById('livenessPrompt');
            let actionIndex = 0;
            let actionFrames = 0;
            let neutralSamples = [];
            let neutralYaw = null;
            let frontalFrames = 0;
            let livenessPassed = false;
            let isVerifying = false;
            let detectionFrameCount = 0;
            const challengeStart = performance.now();
            prompt.textContent = 'Hadap lurus ke kamera untuk mulai';

            async function detectionLoop() {
                if (isVerifying) return;

                detectionFrameCount++;

                if (performance.now() >= window.livenessDeadlineAt) {
                    setStatus('Waktu tantangan habis. Muat ulang halaman untuk mendapatkan tantangan baru.', 'error');
                    document.getElementById('livenessPrompt').textContent = 'Tantangan kedaluwarsa';
                    return;
                }

                try {
                    const detections = await faceapi.detectSingleFace(video, options).withFaceLandmarks(true);

                    ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);

                    if (!detections) {
                        if (detectionFrameCount % 30 === 0) {
                            console.log('Frame', detectionFrameCount, ': No face detected');
                        }
                        actionFrames = 0;
                        confidencePercent.textContent = '0%';
                        confidenceBar.style.width = '0%';
                        setStatus('Wajah tidak terdeteksi. Posisikan wajah ke kamera.', 'neutral');
                        requestAnimationFrame(detectionLoop);
                        return;
                    }

                    const box = detections.detection.box;
                    drawCornerBox(ctx, box, false);
                    const points = detections.landmarks.positions;
                    const eyeMidX = (points[36].x + points[45].x) / 2;
                    const eyeWidth = Math.max(1, points[45].x - points[36].x);
                    const yaw = (points[30].x - eyeMidX) / eyeWidth;
                    const mouth = Math.abs(points[66].y - points[62].y) / eyeWidth;
                    const elapsed = Math.round(performance.now() - challengeStart);
                    if (neutralYaw === null) {
                        neutralSamples.push(yaw);
                        if (neutralSamples.length >= 8) {
                            neutralYaw = neutralSamples.reduce((sum, value) => sum + value, 0) / neutralSamples.length;
                            prompt.textContent = actionLabels[livenessChallenge.actions[0]];
                        } else {
                            setStatus(`Hadap lurus ke kamera... ${neutralSamples.length}/8`, 'info');
                            requestAnimationFrame(detectionLoop);
                            return;
                        }
                    }
                    // face-api landmark x shifts opposite to the direction the person turns.
                    // Invert it so positive yaw means the user's own right.
                    const relativeYaw = neutralYaw - yaw;
                    telemetry.push({ t: elapsed, yaw: Number(relativeYaw.toFixed(3)), mouth: Number(mouth.toFixed(3)) });
                    if (telemetry.length > 400) telemetry.shift();

                    const action = livenessChallenge.actions[actionIndex];
                    const passed = action === 'left' ? relativeYaw <= -0.08 : action === 'right' ? relativeYaw >= 0.08 : mouth >= 0.24;
                    actionFrames = passed ? actionFrames + 1 : 0;
                    if (!livenessPassed) setStatus('Ikuti instruksi liveness sesuai urutan.', 'info');
                    if (actionFrames >= 2) {
                        actionIndex++;
                        actionFrames = 0;
                        if (actionIndex < livenessChallenge.actions.length) {
                            prompt.textContent = actionLabels[livenessChallenge.actions[actionIndex]];
                        } else {
                            livenessPassed = true;
                            prompt.textContent = 'Liveness berhasil ✓. Hadap lurus dan tutup mulut.';
                            setStatus('Liveness berhasil. Menunggu wajah frontal sebelum mencocokkan data...', 'success');
                        }
                    }

                    if (livenessPassed) {
                        const mouthClosed = mouth < 0.24;
                        frontalFrames = Math.abs(relativeYaw) <= 0.06 && mouthClosed ? frontalFrames + 1 : 0;
                        if (frontalFrames >= 4) {
                            isVerifying = true;
                            prompt.textContent = 'Liveness berhasil ✓. Menjalankan pemeriksaan anti-spoofing...';
                            setStatus('Liveness berhasil. Memeriksa sampel kamera dengan PAD...', 'info');
                            const finalFace = await faceapi.detectSingleFace(video, options).withFaceLandmarks(true).withFaceDescriptor();
                            if (!finalFace) {
                                setStatus('Liveness berhasil, tetapi wajah frontal tidak tertangkap. Muat ulang untuk mencoba lagi.', 'error');
                                return;
                            }
                            const padFrames = await capturePadFrames(video, 3);
                            setStatus('Mengirim sampel ke server untuk pemeriksaan PAD...', 'info');
                            await submitVerification(finalFace, padFrames, telemetry, livenessChallenge.nonce, pendingLocation,
                                verifiedAttendanceForm, verifiedLocationInput, verifiedTokenInput, attendanceReadyBox,
                                verifiedAttendanceSubmit, intendedAction);
                            return;
                        }
                    }

                } catch (e) {
                    console.error('Detection error at frame', detectionFrameCount, ':', e);
                }

                requestAnimationFrame(detectionLoop);
            }

            console.log('Starting liveness detection loop with challenge:', livenessChallenge.actions);
            detectionLoop();
        }

        async function capturePadFrames(video, count = 3) {
            const frames = [];
            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;
            const targetAspect = 3 / 4;
            let sourceX = 0;
            let sourceY = 0;
            let sourceWidth = videoWidth;
            let sourceHeight = videoHeight;

            if (sourceWidth / sourceHeight > targetAspect) {
                sourceWidth = sourceHeight * targetAspect;
                sourceX = (videoWidth - sourceWidth) / 2;
            } else {
                sourceHeight = sourceWidth / targetAspect;
                sourceY = (videoHeight - sourceHeight) / 2;
            }

            const canvas = document.createElement('canvas');
            canvas.width = 240;
            canvas.height = 320;
            const context = canvas.getContext('2d');

            for (let index = 0; index < count; index++) {
                if (index > 0) await new Promise(resolve => setTimeout(resolve, 180));
                context.drawImage(video, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, canvas.width, canvas.height);
                frames.push(canvas.toDataURL('image/jpeg', 0.82));
            }

            return frames;
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

        window.addEventListener('pagehide', () => {
            const video = document.getElementById('webcam');
            const stream = video?.srcObject;
            if (stream && typeof stream.getTracks === 'function') {
                stream.getTracks().forEach(track => track.stop());
            }
        });

        async function submitVerification(detections, padFrames, telemetry, challengeNonce, pendingLocation, verifiedAttendanceForm, verifiedLocationInput,
            verifiedTokenInput, attendanceReadyBox, verifiedAttendanceSubmit, intendedAction, retryCount = 0) {
            const descriptor = Array.from(detections.descriptor);
            const statusBox = document.getElementById('statusBox');

            try {
                const res = await fetch('{{ route('api.face.verify') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        descriptor: JSON.stringify(descriptor),
                        pad_frames: padFrames,
                        challenge_nonce: challengeNonce,
                        telemetry,
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
                            detections, padFrames, telemetry, challengeNonce,
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
                        if (errorJson?.pad?.frames?.length) {
                            const frameScores = errorJson.pad.frames.map(frame => frame.real_score.toFixed(2)).join(', ');
                            message += ` Skor real per frame: ${frameScores}.`;
                        }
                    }

                    throw new Error(message);
                }

                const json = await res.json();
                if (json.match && json.verification_token) {
                    const confidence = Math.round((json.similarity || 0) * 100);
                    document.getElementById('confidencePercent').textContent = confidence + '%';
                    document.getElementById('confidenceBar').style.width = confidence + '%';
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
                    const padSummary = json.pad
                        ? ` PAD: ${json.pad.real_frames}/${json.pad.total_frames} frame dinilai asli.`
                        : '';
                    statusBox.textContent = 'Verifikasi berhasil.' + padSummary + ' Silakan klik tombol absen.';
                    setTimeout(() => {
                        statusBox.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 200);
                } else {
                    statusBox.className =
                        'text-sm rounded-xl border px-3 py-2 bg-red-50 text-red-700 border-red-200';
                    statusBox.textContent = json.message || 'Verifikasi gagal. Wajah belum cocok. Coba lagi dengan posisi lebih lurus.';
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

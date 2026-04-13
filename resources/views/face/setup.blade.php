<x-app-layout>
    <style>
        /* Hanya untuk hal yang tidak bisa dilakukan Tailwind */
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

        .face-guide {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: clamp(110px, 38%, 170px);
            height: clamp(130px, 46%, 200px);
            border-radius: 50%;
            border: 2px dashed rgba(255, 255, 255, 0.25);
            pointer-events: none;
            transition: border-color 0.3s, border-style 0.3s;
            z-index: 2;
        }

        .face-guide.detected {
            border-color: #3b82f6;
            border-style: solid;
        }

        .face-guide.captured {
            border-color: #22c55e;
            border-style: solid;
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

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0.3
            }
        }

        .dot-blink {
            animation: blink 1s infinite;
        }

        #preview {
            display: none;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #preview.show {
            display: block;
        }

        #descriptorInfo {
            display: none;
        }

        #descriptorInfo.show {
            display: block;
        }
    </style>

    <div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Alert --}}
        @if (session('success'))
            <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 text-center">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="text-center">
            <h2 class="text-xl font-bold text-gray-800">Registrasi Wajah</h2>
            <p class="text-sm text-gray-500 mt-1">Wajah anda belum terdaftar, silakan lakukan registrasi wajah</p>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Kolom Kiri: Kamera --}}
            <div class="bg-white p-5 rounded-2xl border shadow-sm space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Kamera</p>

                {{-- Camera box --}}
                <div class="relative rounded-xl overflow-hidden border bg-slate-900 aspect-[4/3]" id="camWrapper">
                    <video id="webcam" autoplay muted playsinline class="w-full h-full object-cover block"></video>
                    <canvas id="overlay"></canvas>
                    <div class="face-guide" id="faceGuide"></div>

                    {{-- Loading overlay --}}
                    <div id="camLoading"
                        class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center gap-3 z-10 transition-opacity duration-300">
                        <div class="spinner w-7 h-7 rounded-full border-2 border-white/20 border-t-blue-500"></div>
                        <p id="camLoadingText" class="text-xs text-slate-400">Memuat model AI...</p>
                    </div>
                </div>

                {{-- Status --}}
                <div
                    class="flex items-center gap-2 bg-gray-50 border rounded-xl px-3 py-2 text-sm text-gray-600 min-h-[38px]">
                    <span id="statusDot" class="w-2 h-2 rounded-full bg-gray-300 flex-shrink-0 dot-blink"></span>
                    <span id="statusText">Mempersiapkan kamera...</span>
                </div>

                {{-- Capture Button --}}
                <button type="button" id="captureBtn" disabled
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3" />
                    </svg>
                    Capture Wajah
                </button>
            </div>

            {{-- Kolom Kanan: Hasil --}}
            <div class="bg-white p-5 rounded-2xl border shadow-sm space-y-3">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Hasil Capture</p>

                {{-- Preview --}}
                <div class="relative rounded-xl overflow-hidden border bg-gray-50 aspect-[4/3] flex items-center justify-center"
                    id="previewBox">
                    <div id="previewEmpty" class="text-center text-gray-400">
                        <div class="text-4xl mb-2">👤</div>
                        <p class="text-xs">Belum ada capture</p>
                    </div>
                    <img id="preview" alt="Face preview">
                </div>

                {{-- Descriptor info --}}
                <div id="descriptorInfo"
                    class="bg-green-50 border border-green-100 rounded-xl px-3 py-2 text-xs text-green-700">
                    Descriptor wajah berhasil diambil —
                    <span id="descriptorLen" class="font-semibold">0</span> dimensi
                </div>

                {{-- Form simpan --}}
                <form method="POST" action="{{ route('face.setup.store') }}" id="saveForm">
                    @csrf
                    <input type="hidden" name="face_descriptor" id="face_descriptor">
                    <input type="hidden" name="face_image_base64" id="face_image_base64">

                    <button type="submit" id="saveBtn" disabled
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                        </svg>
                        Simpan Data Wajah
                    </button>
                </form>

                {{-- Tips --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-xs text-blue-700 leading-relaxed">
                    <p class="font-semibold mb-1">💡 Tips foto terbaik:</p>
                    Wajah menghadap lurus • Cahaya cukup • Tidak pakai kacamata gelap / masker • Pastikan lingkaran biru
                    muncul sebelum capture
                </div>
            </div>

        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const waitForFaceApi = setInterval(() => {
                if (typeof faceapi !== 'undefined') {
                    clearInterval(waitForFaceApi);
                    initFaceSetup();
                }
            }, 100);
        });

        async function initFaceSetup() {
            const video = document.getElementById('webcam');
            const overlayEl = document.getElementById('overlay');
            const preview = document.getElementById('preview');
            const previewEmpty = document.getElementById('previewEmpty');
            const captureBtn = document.getElementById('captureBtn');
            const saveBtn = document.getElementById('saveBtn');
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');
            const camLoading = document.getElementById('camLoading');
            const camLoadingText = document.getElementById('camLoadingText');
            const faceGuide = document.getElementById('faceGuide');
            const descriptorInfo = document.getElementById('descriptorInfo');
            const descriptorLen = document.getElementById('descriptorLen');
            const descriptorInput = document.getElementById('face_descriptor');
            const imageBase64Input = document.getElementById('face_image_base64');

            function setStatus(type, msg) {
                const colors = {
                    loading: 'bg-amber-400',
                    scanning: 'bg-blue-500',
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                };
                statusDot.className = `w-2 h-2 rounded-full flex-shrink-0 dot-blink ${colors[type] || 'bg-gray-300'}`;
                statusText.textContent = msg;
            }

            // ─── 1. Load Models ───────────────────────────────────────
            try {
                camLoadingText.textContent = 'Memuat model AI...';
                setStatus('loading', 'Memuat model AI...');
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                ]);
            } catch (e) {
                setStatus('error', 'Gagal memuat model. Cek folder /public/models/');
                camLoadingText.textContent = 'Gagal memuat model AI';
                return;
            }

            // ─── 2. Start Camera otomatis ─────────────────────────────
            try {
                camLoadingText.textContent = 'Mengakses kamera...';
                setStatus('loading', 'Mengakses kamera...');
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

                // Sembunyikan loading overlay
                camLoading.classList.add('cam-loading-hide');
                setStatus('scanning', 'Arahkan wajah ke lingkaran panduan...');
                captureBtn.disabled = false;

                // ─── 3. Deteksi wajah realtime (hanya untuk guide visual) ──
                startLiveDetection(video, overlayEl, faceGuide, setStatus);

            } catch (e) {
                setStatus('error', 'Kamera tidak dapat diakses: ' + e.message);
                camLoadingText.textContent = e.message;
            }

            // ─── 4. Capture ───────────────────────────────────────────
            captureBtn.addEventListener('click', async () => {
                captureBtn.disabled = true;
                captureBtn.textContent = 'Memproses...';
                setStatus('loading', 'Mendeteksi wajah...');

                try {
                    const result = await faceapi
                        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks(true)
                        .withFaceDescriptor();

                    if (!result) {
                        setStatus('error', 'Wajah tidak terdeteksi. Perbaiki posisi & pencahayaan.');
                        captureBtn.disabled = false;
                        captureBtn.innerHTML = `
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/>
                        </svg>
                        Coba Lagi
                    `;
                        return;
                    }

                    // Gambar kotak wajah di overlay
                    const ctx = overlayEl.getContext('2d');
                    ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);
                    const box = result.detection.box;
                    ctx.strokeStyle = '#22c55e';
                    ctx.lineWidth = 2;
                    drawCornerBox(ctx, box);

                    faceGuide.className = 'face-guide captured';

                    // Screenshot frame
                    const shot = document.createElement('canvas');
                    shot.width = video.videoWidth;
                    shot.height = video.videoHeight;
                    shot.getContext('2d').drawImage(video, 0, 0);
                    const dataUrl = shot.toDataURL('image/jpeg', 0.9);

                    // Tampilkan preview
                    preview.src = dataUrl;
                    preview.classList.add('show');
                    previewEmpty.style.display = 'none';

                    // Isi descriptor
                    const descriptor = Array.from(result.descriptor);
                    descriptorInput.value = JSON.stringify(descriptor);
                    descriptorLen.textContent = descriptor.length;
                    descriptorInfo.classList.add('show');
                    imageBase64Input.value = dataUrl;

                    setStatus('success', 'Data wajah siap disimpan!');
                    saveBtn.disabled = false;
                    captureBtn.innerHTML = `
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/>
                    </svg>
                    Capture Ulang
                `;
                    captureBtn.disabled = false;

                } catch (e) {
                    setStatus('error', 'Proses capture gagal: ' + e.message);
                    captureBtn.disabled = false;
                    captureBtn.textContent = 'Coba Lagi';
                }
            });
        }

        // ─── Deteksi wajah realtime untuk panduan visual ──────────────
        async function startLiveDetection(video, overlayEl, faceGuide, setStatus) {
            const options = new faceapi.TinyFaceDetectorOptions({
                inputSize: 224,
                scoreThreshold: 0.5
            });
            const ctx = overlayEl.getContext('2d');

            async function loop() {
                if (!video.paused && !video.ended) {
                    const det = await faceapi.detectSingleFace(video, options);
                    ctx.clearRect(0, 0, overlayEl.width, overlayEl.height);

                    if (det) {
                        faceGuide.className = 'face-guide detected';
                    } else {
                        faceGuide.className = 'face-guide';
                    }
                }
                requestAnimationFrame(loop);
            }
            loop();
        }

        // ─── Sudut kotak wajah ────────────────────────────────────────
        function drawCornerBox(ctx, box) {
            const {
                x,
                y,
                width: w,
                height: h
            } = box;
            const len = 14;
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
    </script>
</x-app-layout>

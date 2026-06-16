<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Check-in Scanner — ') . $event->title }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="checkinScanner('{{ route('organizer.events.checkin.scan', $event->id) }}', @js($initialScanHistory))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('organizer.events.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Events
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_430px] lg:items-start">
                <div class="space-y-6">
            <!-- Camera Scanner Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ cameraOpen: false }">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-900">QR Scanner</h3>
                    <button type="button" 
                        @click="cameraOpen = !cameraOpen; cameraOpen ? startCamera() : stopCamera()"
                        class="px-4 py-2 rounded-lg bg-[#10367d] text-white font-bold text-sm hover:bg-[#0c2a61] transition shadow-md">
                        <span x-text="cameraOpen ? 'Tutup Kamera' : 'Buka Kamera'"></span>
                    </button>
                </div>
                
                <div x-show="cameraOpen" class="p-6 text-center" style="display: none;">
                    <p id="camera-error" class="text-red-600 font-medium mb-3 hidden">Browser tidak mendukung akses kamera</p>
                    <p class="text-sm text-slate-500 mb-4" x-text="loading ? 'Verifying ticket...' : 'Arahkan kamera ke QR Code tiket attendee'"></p>
                    
                    <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-xl bg-black aspect-video flex items-center justify-center">
                        <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline></video>
                        <canvas id="camera-canvas" style="display:none"></canvas>
                        <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                            <div class="absolute inset-0 bg-slate-950/10"></div>
                            <svg id="qr-detection-overlay" class="absolute inset-0 h-full w-full opacity-0 transition-opacity duration-100" preserveAspectRatio="none">
                                <polygon id="qr-detection-polygon" points="" fill="rgba(37,99,235,0.14)" stroke="#60a5fa" stroke-width="4" stroke-linejoin="round"></polygon>
                                <polygon id="qr-detection-polygon-inner" points="" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-linejoin="round"></polygon>
                            </svg>
                            <div class="absolute bottom-3 rounded-full bg-slate-950/70 px-3 py-1 text-[11px] font-bold text-white">
                                Arahkan kamera ke QR, border akan mengikuti kode
                            </div>
                        </div>
                        <div x-show="loading" x-transition.opacity class="absolute inset-0 flex items-center justify-center bg-slate-950/50 text-white text-sm font-bold" style="display: none;">
                            Verifying...
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t border-slate-100 pt-5 text-left max-w-sm mx-auto">
                        <p class="text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">Atau upload gambar QR (Testing)</p>
                        <input type="file" id="qr-upload" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-100 bg-slate-50 text-center">
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Ticket Check-in</h3>
                    <p class="text-slate-500">Scan or manually enter the ticket code to verify attendee.</p>
                </div>
                
                <div class="p-8">
                    <form @submit.prevent="scanTicket" class="space-y-6">
                        <div>
                            <label for="ticket_code" class="block text-sm font-bold text-slate-700 mb-1">Ticket Code</label>
                            <input type="text" id="ticket_code" x-model="ticketCode" x-ref="ticketInput"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-lg text-center uppercase tracking-widest font-mono font-bold"
                                placeholder="E.g. EVR-123456" required autocomplete="off">
                        </div>
                        
                        <button type="submit" :disabled="loading"
                            class="w-full flex items-center justify-center px-6 py-4 rounded-xl bg-[#10367d] text-white font-bold text-lg hover:bg-[#0c2a61] transition shadow-md hover:shadow-lg disabled:opacity-70">
                            <svg x-show="loading" style="display: none;" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="loading ? 'Verifying...' : 'Scan / Check Ticket'"></span>
                        </button>
                    </form>
                </div>
            </div>
                </div>

                <aside class="space-y-6 lg:sticky lg:top-24">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                            <h3 class="text-lg font-black text-slate-900">Live Gate Monitor</h3>
                            <p class="text-sm text-slate-500">Latest result and scan status stay visible here.</p>
                        </div>
                        <div class="p-5">
                    <div x-show="!message" class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.75h6.5v6.5h-6.5zM13.75 4.75h6.5v6.5h-6.5zM3.75 14.75h6.5v6.5h-6.5zM14.75 14.75h2.5m-2.5 3h5.5m-2.5-5.5v8.5"></path></svg>
                        </div>
                        <h4 class="text-lg font-black text-blue-800">Ready to Scan</h4>
                        <p class="mt-1 text-sm font-semibold text-blue-600">Arahkan kamera ke tiket berikutnya.</p>
                    </div>

                    <!-- Result Area -->
                    <div x-show="message" x-transition.opacity style="display: none;">
                        <!-- Success -->
                        <div x-show="statusType === 'checked_in'" style="display: none;" class="p-5 bg-green-50 rounded-2xl border border-green-100 flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-green-100 text-green-600 flex items-center justify-center rounded-full mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-green-800 mb-1">Check-in Successful!</h4>
                            <div class="bg-white px-6 py-4 rounded-xl border border-green-100 w-full mt-4 text-left">
                                <p class="text-sm text-slate-500 mb-1">Attendee Name</p>
                                <p class="font-bold text-slate-900 mb-3" x-text="result.buyer_name"></p>
                                
                                <p class="text-sm text-slate-500 mb-1">Ticket Type</p>
                                <p class="font-bold text-slate-900 mb-3" x-text="result.ticket_name"></p>
                                
                                <p class="text-sm text-slate-500 mb-1">Check-in Time</p>
                                <p class="font-bold text-slate-900" x-text="result.checkin_time"></p>
                            </div>
                        </div>

                        <!-- Already Used -->
                        <div x-show="statusType === 'already_used'" style="display: none;" class="p-5 bg-amber-50 rounded-2xl border border-amber-100 flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-amber-100 text-amber-600 flex items-center justify-center rounded-full mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 3a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-amber-800 mb-2">Already Checked In</h4>
                            <p class="text-amber-700" x-text="message"></p>
                            <div class="bg-white px-6 py-4 rounded-xl border border-amber-100 w-full mt-4 text-left">
                                <p class="text-sm text-slate-500 mb-1">Attendee Name</p>
                                <p class="font-bold text-slate-900 mb-3" x-text="result.buyer_name || '-'"></p>
                                
                                <p class="text-sm text-slate-500 mb-1">Ticket Type</p>
                                <p class="font-bold text-slate-900 mb-3" x-text="result.ticket_name || '-'"></p>
                                
                                <p class="text-sm text-slate-500 mb-1">Previous Check-in</p>
                                <p class="font-bold text-slate-900" x-text="result.checkin_time || '-'"></p>
                            </div>
                        </div>

                        <!-- Error -->
                        <div x-show="statusType === 'failed'" style="display: none;" class="p-5 bg-red-50 rounded-2xl border border-red-100 flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-red-100 text-red-600 flex items-center justify-center rounded-full mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-red-800 mb-2">Check-in Failed</h4>
                            <p class="text-red-600" x-text="message"></p>
                        </div>
                    </div>

                    <!-- Scan History -->
                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div>
                                <h4 class="text-lg font-black text-slate-900">Scan History</h4>
                                <p class="text-sm text-slate-500">Latest scan results for this session.</p>
                            </div>
                        </div>

                        <div class="mb-4 grid grid-cols-3 gap-3" x-show="scanHistory.length > 0" style="display: none;">
                            <div class="rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-center">
                                <p class="text-lg font-black text-green-700" x-text="historyCount('checked_in')"></p>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-green-700">Checked In</p>
                            </div>
                            <div class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-center">
                                <p class="text-lg font-black text-amber-700" x-text="historyCount('already_used')"></p>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-amber-700">Already Used</p>
                            </div>
                            <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-center">
                                <p class="text-lg font-black text-red-700" x-text="historyCount('failed')"></p>
                                <p class="text-[11px] font-bold uppercase tracking-wide text-red-700">Failed</p>
                            </div>
                        </div>

                        <div x-show="scanHistory.length === 0" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-6 text-center text-sm font-semibold text-slate-400">
                            No tickets scanned yet.
                        </div>

                        <div x-show="scanHistory.length > 0" class="max-h-[420px] space-y-3 overflow-y-auto pr-1" style="display: none;">
                            <template x-for="item in scanHistory" :key="item.id">
                                <div
                                    class="flex items-start gap-4 rounded-2xl border bg-white p-4"
                                    :class="{
                                        'border-green-100': item.type === 'checked_in',
                                        'border-amber-100': item.type === 'already_used',
                                        'border-red-100': item.type === 'failed'
                                    }"
                                >
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-600': item.type === 'checked_in',
                                            'bg-amber-100 text-amber-600': item.type === 'already_used',
                                            'bg-red-100 text-red-600': item.type === 'failed'
                                        }"
                                    >
                                        <svg x-show="item.type === 'checked_in'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <svg x-show="item.type === 'already_used'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 3a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                                        <svg x-show="item.type === 'failed'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p
                                                    class="text-sm font-black"
                                                    :class="{
                                                        'text-green-700': item.type === 'checked_in',
                                                        'text-amber-700': item.type === 'already_used',
                                                        'text-red-700': item.type === 'failed'
                                                    }"
                                                    x-text="item.label"
                                                ></p>
                                                <p class="mt-1 truncate text-sm font-bold text-slate-900" x-text="item.name"></p>
                                                <p class="mt-0.5 truncate text-xs font-semibold text-slate-500" x-text="item.ticket"></p>
                                            </div>
                                            <p class="shrink-0 text-xs font-bold text-slate-400" x-text="item.time"></p>
                                        </div>
                                        <p class="mt-2 text-xs font-semibold text-slate-500" x-text="item.message"></p>
                                        <p class="mt-1 font-mono text-[11px] font-bold uppercase tracking-widest text-slate-400" x-text="item.code"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
                </aside>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkinScanner', (scanUrl, initialHistory = []) => ({
                ticketCode: '',
                loading: false,
                success: null,
                statusType: 'ready',
                message: '',
                result: {},
                scanHistory: initialHistory,
                lastScannedCode: '',
                lastScannedAt: 0,
                resumeAt: 0,
                
                init() {
                    this.$nextTick(() => { this.$refs.ticketInput.focus(); });
                },

                async scanTicket() {
                    if (!this.ticketCode.trim()) return;
                    if (this.loading) return;

                    const scannedCode = this.ticketCode.trim();
                    
                    this.loading = true;
                    this.message = '';
                    this.success = null;
                    this.statusType = 'ready';
                    
                    try {
                        const response = await fetch(scanUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ticket_code: this.ticketCode })
                        });
                        
                        const data = await response.json();
                        
                        this.success = data.success;
                        this.statusType = data.status_type || (data.success ? 'checked_in' : 'failed');
                        this.result = {
                            buyer_name: data.buyer_name,
                            ticket_name: data.ticket_name,
                            checkin_time: data.checkin_time
                        };
                        
                        if (this.success) {
                            this.message = 'Success';
                            this.playScanSound('checked_in');
                            this.ticketCode = ''; // clear for next scan
                        } else {
                            this.message = data.message || 'Unknown error occurred.';
                            this.playScanSound(this.statusType);
                        }

                        this.addScanHistory(scannedCode);
                    } catch (error) {
                        this.success = false;
                        this.statusType = 'failed';
                        this.message = 'Network error or server unavailable.';
                        this.result = {};
                        this.playScanSound('failed');
                        this.addScanHistory(scannedCode);
                    } finally {
                        this.loading = false;
                        this.resumeAt = Date.now() + 450;
                        this.$nextTick(() => { this.$refs.ticketInput.focus(); });
                    }
                },

                addScanHistory(code) {
                    const labels = {
                        checked_in: 'Checked In',
                        already_used: 'Already Used',
                        failed: 'Failed'
                    };

                    this.scanHistory.unshift({
                        id: Date.now() + '-' + Math.random().toString(16).slice(2),
                        type: this.statusType,
                        label: labels[this.statusType] || 'Scan Result',
                        name: this.result.buyer_name || 'Unknown attendee',
                        ticket: this.result.ticket_name || 'No ticket detail',
                        code: code || '-',
                        message: this.message || '-',
                        time: new Date().toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        })
                    });

                    this.scanHistory = this.scanHistory.slice(0, 20);
                },

                historyCount(type) {
                    return this.scanHistory.filter((item) => item.type === type).length;
                },

                handleDetectedCode(code) {
                    const normalizedCode = String(code || '').trim();
                    const now = Date.now();

                    if (!normalizedCode || this.loading || now < this.resumeAt) return;
                    if (normalizedCode === this.lastScannedCode && now - this.lastScannedAt < 1800) return;

                    this.lastScannedCode = normalizedCode;
                    this.lastScannedAt = now;
                    this.ticketCode = normalizedCode;
                    this.$refs.ticketInput.value = normalizedCode;
                    this.scanTicket();
                },

                playScanSound(type) {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;

                    const context = new AudioContext();
                    const playTone = (frequency, duration, delay = 0, volume = 0.08) => {
                        const oscillator = context.createOscillator();
                        const gain = context.createGain();
                        oscillator.type = 'sine';
                        oscillator.frequency.value = frequency;
                        gain.gain.value = volume;
                        oscillator.connect(gain);
                        gain.connect(context.destination);

                        const startAt = context.currentTime + delay;
                        oscillator.start(startAt);
                        gain.gain.exponentialRampToValueAtTime(0.0001, startAt + duration);
                        oscillator.stop(startAt + duration);
                    };

                    if (type === 'checked_in') {
                        playTone(880, 0.12);
                    } else if (type === 'already_used') {
                        playTone(620, 0.1);
                        playTone(620, 0.1, 0.16);
                    } else {
                        playTone(220, 0.22, 0, 0.1);
                        playTone(180, 0.18, 0.24, 0.1);
                    }
                }
            }));
        });
    </script>

    <script>
        let cameraStream = null;
        let scanInterval = null;
        let qrOutlineTimeout = null;
        const SCAN_INTERVAL_MS = 45;
        const SCAN_MAX_CANVAS_SIZE = 560;

        async function startCamera() {
            const video = document.getElementById('camera-preview');
            const errorEl = document.getElementById('camera-error');
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                errorEl.classList.remove('hidden');
                errorEl.textContent = 'Browser tidak mendukung akses kamera';
                return;
            }

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 960 },
                        height: { ideal: 720 }
                    }
                });
                video.srcObject = cameraStream;
                errorEl.classList.add('hidden');
                
                video.onloadeddata = () => {
                    if (scanInterval) {
                        clearInterval(scanInterval);
                    }

                    scanInterval = setInterval(scanFrame, SCAN_INTERVAL_MS);
                };
            } catch (err) {
                console.error("Error accessing camera:", err);
                errorEl.classList.remove('hidden');
                errorEl.textContent = 'Browser tidak mendukung akses kamera atau izin ditolak';
            }
        }

        function stopCamera() {
            if (scanInterval) {
                clearInterval(scanInterval);
                scanInterval = null;
            }
            hideQrDetectionOutline();
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        function scanFrame() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');
            const scannerData = getCheckinScannerData();
            
            if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) return;
            if (scannerData && (scannerData.loading || Date.now() < scannerData.resumeAt)) return;

            const scale = Math.min(1, SCAN_MAX_CANVAS_SIZE / Math.max(video.videoWidth, video.videoHeight));
            const scanWidth = Math.floor(video.videoWidth * scale);
            const scanHeight = Math.floor(video.videoHeight * scale);

            canvas.height = scanHeight;
            canvas.width = scanWidth;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, video.videoWidth, video.videoHeight, 0, 0, scanWidth, scanHeight);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "attemptBoth",
            });

            if (code) {
                showQrDetectionOutline(video, code.location, {
                    sourceX: 0,
                    sourceY: 0,
                    sourceWidth: video.videoWidth,
                    sourceHeight: video.videoHeight,
                    scanWidth,
                    scanHeight,
                });
                processQRResult(code.data);
            } else {
                scheduleQrDetectionOutlineHide();
            }
        }

        function showQrDetectionOutline(video, location, scanArea) {
            const overlay = document.getElementById('qr-detection-overlay');
            const polygon = document.getElementById('qr-detection-polygon');
            const innerPolygon = document.getElementById('qr-detection-polygon-inner');

            if (!overlay || !polygon || !innerPolygon || !location) return;

            const points = [
                location.topLeftCorner,
                location.topRightCorner,
                location.bottomRightCorner,
                location.bottomLeftCorner,
            ].map((point) => mapCanvasPointToVideoElement(video, point, scanArea));

            const pointString = points.map((point) => `${point.x},${point.y}`).join(' ');
            polygon.setAttribute('points', pointString);
            innerPolygon.setAttribute('points', pointString);
            overlay.classList.remove('opacity-0');
            overlay.classList.add('opacity-100');

            if (qrOutlineTimeout) {
                clearTimeout(qrOutlineTimeout);
                qrOutlineTimeout = null;
            }
        }

        function mapCanvasPointToVideoElement(video, point, scanArea) {
            const sourceX = scanArea.sourceX + (point.x / scanArea.scanWidth) * scanArea.sourceWidth;
            const sourceY = scanArea.sourceY + (point.y / scanArea.scanHeight) * scanArea.sourceHeight;
            const videoScale = Math.max(video.clientWidth / video.videoWidth, video.clientHeight / video.videoHeight);
            const renderedWidth = video.videoWidth * videoScale;
            const renderedHeight = video.videoHeight * videoScale;
            const offsetX = (video.clientWidth - renderedWidth) / 2;
            const offsetY = (video.clientHeight - renderedHeight) / 2;

            return {
                x: Math.round(offsetX + sourceX * videoScale),
                y: Math.round(offsetY + sourceY * videoScale),
            };
        }

        function scheduleQrDetectionOutlineHide() {
            if (qrOutlineTimeout) return;

            qrOutlineTimeout = setTimeout(() => {
                hideQrDetectionOutline();
            }, 220);
        }

        function hideQrDetectionOutline() {
            const overlay = document.getElementById('qr-detection-overlay');

            if (qrOutlineTimeout) {
                clearTimeout(qrOutlineTimeout);
                qrOutlineTimeout = null;
            }

            if (overlay) {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
            }
        }

function processQRResult(resultText) {
    console.log("QR Code detected:", resultText);

    const ticketInput = document.getElementById('ticket_code');
    const alpineData = getCheckinScannerData();
    
    if (ticketInput && alpineData) {
        alpineData.handleDetectedCode(resultText);
    }

    const qrUpload = document.getElementById('qr-upload');
    if (qrUpload) qrUpload.value = '';
}

        function getCheckinScannerData() {
            const scannerEl = document.querySelector('[x-data*="checkinScanner"]');

            return scannerEl && window.Alpine ? window.Alpine.$data(scannerEl) : null;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const qrUpload = document.getElementById('qr-upload');
            if (qrUpload) {
                qrUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = new Image();
                        img.onload = async function() {
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            
                            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "attemptBoth",
                            });
                            
                            if (code) {
                                processQRResult(code.data);
                            } else {
                                await evModal.alert({
                                    title: 'QR Code Tidak Ditemukan',
                                    message: 'QR Code tidak ditemukan pada gambar ini. Coba gambar yang lebih jelas.',
                                    icon: 'danger',
                                });
                                qrUpload.value = '';
                            }
                        }
                        img.src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>

</x-app-layout>

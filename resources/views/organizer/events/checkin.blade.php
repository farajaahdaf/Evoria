<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Check-in Scanner — ') . $event->title }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="checkinScanner('{{ route('organizer.events.checkin.scan', $event->id) }}')">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('organizer.events.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium flex items-center gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Events
                </a>
            </div>

            <!-- Camera Scanner Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6" x-data="{ cameraOpen: false }">
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
                    <p class="text-sm text-slate-500 mb-4">Arahkan kamera ke QR Code tiket attendee</p>
                    
                    <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-xl bg-black aspect-video flex items-center justify-center">
                        <video id="camera-preview" class="w-full h-full object-cover" autoplay playsinline></video>
                        <canvas id="camera-canvas" style="display:none"></canvas>
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

                    <!-- Result Area -->
                    <div x-show="message" x-transition.opacity class="mt-8" style="display: none;">
                        <!-- Success -->
                        <div x-show="success" style="display: none;" class="p-6 bg-green-50 rounded-2xl border border-green-100 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-green-100 text-green-600 flex items-center justify-center rounded-full mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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

                        <!-- Error -->
                        <div x-show="!success" style="display: none;" class="p-6 bg-red-50 rounded-2xl border border-red-100 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-red-100 text-red-600 flex items-center justify-center rounded-full mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-red-800 mb-2">Check-in Failed</h4>
                            <p class="text-red-600" x-text="message"></p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkinScanner', (scanUrl) => ({
                ticketCode: '',
                loading: false,
                success: null,
                message: '',
                result: {},
                
                init() {
                    this.$nextTick(() => { this.$refs.ticketInput.focus(); });
                },

                async scanTicket() {
                    if (!this.ticketCode.trim()) return;
                    
                    this.loading = true;
                    this.message = '';
                    this.success = null;
                    
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
                        
                        if (this.success) {
                            this.message = 'Success';
                            this.result = {
                                buyer_name: data.buyer_name,
                                ticket_name: data.ticket_name,
                                checkin_time: data.checkin_time
                            };
                            this.ticketCode = ''; // clear for next scan
                        } else {
                            this.message = data.message || 'Unknown error occurred.';
                        }
                    } catch (error) {
                        this.success = false;
                        this.message = 'Network error or server unavailable.';
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => { this.$refs.ticketInput.focus(); });
                    }
                }
            }));
        });
    </script>

    <script>
        let cameraStream = null;
        let scanInterval = null;

        async function startCamera() {
            const video = document.getElementById('camera-preview');
            const errorEl = document.getElementById('camera-error');
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                errorEl.classList.remove('hidden');
                errorEl.textContent = 'Browser tidak mendukung akses kamera';
                return;
            }

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = cameraStream;
                errorEl.classList.add('hidden');
                
                video.onloadeddata = () => {
                    scanInterval = setInterval(scanFrame, 300);
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
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
        }

        function scanFrame() {
            const video = document.getElementById('camera-preview');
            const canvas = document.getElementById('camera-canvas');
            
            if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) return;

            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });

            if (code) {
                processQRResult(code.data);
            }
        }

function processQRResult(resultText) {
    console.log("QR Code detected:", resultText);

    stopCamera();

    // Auto-fill input manual
    const ticketInput = document.getElementById('ticket_code');
    const scannerEl = document.querySelector('[x-data*="checkinScanner"]');
    
    if (ticketInput && scannerEl && window.Alpine) {
        // Sync with Alpine data directly
        window.Alpine.$data(scannerEl).ticketCode = resultText;
        
        // Ensure DOM value is also set visually
        ticketInput.value = resultText;

        // Tunggu Alpine reaktif dulu baru trigger scanTicket langsung
        setTimeout(() => {
            const alpineData = window.Alpine.$data(scannerEl);
            if (alpineData && alpineData.ticketCode) {
                alpineData.scanTicket();
            }
        }, 300);
    }

    // Tutup kamera via Alpine v3
    const cameraCard = document.querySelector('[x-data*="cameraOpen"]');
    if (cameraCard && window.Alpine) {
        window.Alpine.$data(cameraCard).cameraOpen = false;
    }

    const qrUpload = document.getElementById('qr-upload');
    if (qrUpload) qrUpload.value = '';
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
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.width = img.width;
                            canvas.height = img.height;
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                            
                            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                inversionAttempts: "dontInvert",
                            });
                            
                            if (code) {
                                processQRResult(code.data);
                            } else {
                                alert("QR Code tidak ditemukan pada gambar ini. Coba gambar yang lebih jelas.");
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

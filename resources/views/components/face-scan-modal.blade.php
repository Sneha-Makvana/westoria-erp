<!-- Face Scan Modal -->
<div class="modal fade" id="faceScanModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Face</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopFaceScan()">x</button>
            </div>
            <div class="modal-body text-center" style="position: relative;">
                <div id="faceScanStatus" class="mb-2 text-warning">Loading face detection models...</div>
                <div style="position: relative; display: inline-block; max-width: 100%; width: 100%;">
                    <video id="faceVideo" autoplay muted playsinline style="width: 100%; max-width: 400px; border-radius: 8px; background: #000;"></video>
                    <canvas id="faceCanvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="stopFaceScan()">Cancel</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script>
    let faceStream = null;
    let faceDetectionInterval = null;
    let modelsLoaded = false;

    async function loadModels() {
        if (modelsLoaded) return true;
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            modelsLoaded = true;
            return true;
        } catch (e) {
            console.error("Failed to load face models", e);
            $('#faceScanStatus').text('Failed to load models. Try again.');
            return false;
        }
    }

    async function startFaceScan() {
        const video = document.getElementById('faceVideo');
        $('#faceScanStatus').text('Starting camera...');
        
        try {
            faceStream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = faceStream;
            video.addEventListener('play', () => {
                $('#faceScanStatus').text('Please look at the camera...');
                const canvas = document.getElementById('faceCanvas');
                
                setTimeout(() => {
                    const displaySize = { width: video.offsetWidth, height: video.offsetHeight };
                    faceapi.matchDimensions(canvas, displaySize);
                    
                    let countdown = 5;
                    const statusEl = $('#faceScanStatus');
                    
                    const countInterval = setInterval(() => {
                        statusEl.text(`Please position your face. Scanning in ${countdown}...`);
                        countdown--;
                        if (countdown < 0) {
                            clearInterval(countInterval);
                            statusEl.text('Scanning now... Please hold still.');
                            
                            faceDetectionInterval = setInterval(async () => {
                                const detections = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
                                
                                if (detections) {
                                    // Draw bounding box
                                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                                    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                                    faceapi.draw.drawDetections(canvas, resizedDetections);
                                    
                                    // Capture the current frame from the video
                                    const captureCanvas = document.createElement('canvas');
                                    captureCanvas.width = video.videoWidth;
                                    captureCanvas.height = video.videoHeight;
                                    captureCanvas.getContext('2d').drawImage(video, 0, 0);
                                    const faceDataUrl = captureCanvas.toDataURL('image/jpeg');
                                    
                                    // Face captured!
                                    stopFaceScan();
                                    $('#faceScanModal').modal('hide');
                                    
                                    Swal.fire({
                                        title: "Face Captured",
                                        text: "Processing...",
                                        icon: "info",
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                    
                                    // Convert Float32Array to normal array
                                    const embeddingArray = Array.from(detections.descriptor);
                                    
                                    if (typeof window.onFaceCaptured === 'function') {
                                        window.onFaceCaptured(embeddingArray, faceDataUrl);
                                    }
                                }
                            }, 500);
                        }
                    }, 1000); // 1-second countdown ticks
                    
                }, 500); // give video a short moment to render and take its size
            });
        } catch (err) {
            $('#faceScanStatus').removeClass('text-warning').addClass('text-danger').text('Camera access denied or unavailable.');
            console.error(err);
        }
    }

    function stopFaceScan() {
        if (faceDetectionInterval) {
            clearInterval(faceDetectionInterval);
            faceDetectionInterval = null;
        }
        if (faceStream) {
            faceStream.getTracks().forEach(track => track.stop());
            faceStream = null;
        }
        const canvas = document.getElementById('faceCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    $(document).ready(function() {
        $('#faceScanModal').on('hidden.bs.modal', function () {
            stopFaceScan();
        });
        
        // This is generic. The button that opens it needs this class or ID.
        $('.btn-open-face-scan').click(async function() {
            window.faceScanTarget = $(this).data('target') || 'pos_match';
            
            // If they are scanning from QAC, we might need to temporarily hide QAC modal so the camera modal can be seen properly?
            // Actually bootstrap handles nested modals okay, but faceScanModal has a high z-index.
            
            $('#faceScanModal').modal('show');
            $('#faceScanStatus').removeClass('text-success text-danger').addClass('text-warning').text('Loading face detection models...');
            
            if (await loadModels()) {
                startFaceScan();
            }
        });
    });
</script>
@endpush

(function (window, document) {
    const DEFAULT_MODEL_URLS = [
        '/face-api-models',
        'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights'
    ];

    class FaceRecognitionModal {
        constructor(config = {}) {
            this.config = {
                modelUrls: config.modelUrls || DEFAULT_MODEL_URLS,
            };

            this.modalElement = document.getElementById('faceRecognitionModal');
            this.videoElement = document.getElementById('faceRecognitionVideo');
            this.canvasElement = document.getElementById('faceRecognitionCanvas');
            this.statusElement = document.getElementById('faceRecognitionStatus');
            this.matchInfoElement = document.getElementById('faceRecognitionMatchInfo');
            this.captureButton = document.getElementById('faceRecognitionCaptureBtn');
            this.titleElement = document.getElementById('faceRecognitionModalLabel');
            this.subtitleElement = document.getElementById('faceRecognitionModalSubtitle');
            const bootstrapLib = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
            this.bootstrapModal = (this.modalElement && bootstrapLib) ? new bootstrapLib.Modal(this.modalElement) : null;

            this.stream = null;
            this.modelsLoaded = false;
            this.activeOptions = null;

            if (!this.modalElement || !this.videoElement || !this.captureButton || !this.bootstrapModal) {
                console.error('Face recognition modal markup or Bootstrap is missing.', {
                    modal: !!this.modalElement,
                    video: !!this.videoElement,
                    captureBtn: !!this.captureButton,
                    bootstrap: !!this.bootstrapModal
                });
                throw new Error('Face recognition modal markup or Bootstrap is missing.');
            }

            this.captureButton.addEventListener('click', () => this.captureFace());
            this.modalElement.addEventListener('hidden.bs.modal', () => this.cleanup());
        }

        async open(options = {}) {
            this.activeOptions = {
                title: options.title || 'Face Recognition',
                subtitle: options.subtitle || 'Align your face inside the camera frame.',
                captureLabel: options.captureLabel || 'Capture Face',
                onCapture: options.onCapture || null,
            };

            this.titleElement.textContent = this.activeOptions.title;
            this.subtitleElement.textContent = this.activeOptions.subtitle;
            this.captureButton.textContent = this.activeOptions.captureLabel;
            this.showMatchInfo('');
            this.setStatus('Loading face models...', 'info');
            this.setBusy(true);
            this.bootstrapModal.show();

            try {
                await this.ensureModels();
                await this.startCamera();
                this.setStatus('Camera ready. Center your face and capture.', 'success');
            } catch (error) {
                this.setStatus(this.getFriendlyErrorMessage(error), 'danger');
            } finally {
                this.setBusy(false);
            }
        }

        async ensureModels() {
            if (this.modelsLoaded) {
                return;
            }

            if (!window.faceapi) {
                throw new Error('face-api.js failed to load.');
            }

            let lastError = null;

            for (const modelUrl of this.config.modelUrls) {
                try {
                    await Promise.all([
                        window.faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl),
                        window.faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
                        window.faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl),
                    ]);

                    this.modelsLoaded = true;
                    return;
                } catch (error) {
                    lastError = error;
                }
            }

            throw new Error(lastError?.message || 'Unable to load face recognition models.');
        }

        async startCamera() {
            this.stopCamera();

            this.stream = await navigator.mediaDevices.getUserMedia({
                audio: false,
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                },
            });

            this.videoElement.srcObject = this.stream;

            await new Promise((resolve) => {
                this.videoElement.onloadedmetadata = () => {
                    this.videoElement.play().then(resolve).catch(resolve);
                };
            });
        }

        async captureFace() {
            if (!this.activeOptions?.onCapture) {
                return;
            }

            this.setBusy(true);
            this.setStatus('Capturing face descriptor...', 'info');

            try {
                const detection = await window.faceapi
                    .detectSingleFace(this.videoElement, new window.faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (!detection?.descriptor) {
                    throw new Error('No face detected. Please look directly at the camera and try again.');
                }

                const descriptor = Array.from(detection.descriptor);
                
                // Also capture the image from the canvas
                const imageDataUrl = this.videoElement.toDataURL ? this.videoElement.toDataURL('image/jpeg') : null;
                // If videoElement.toDataURL doesn't exist (it usually doesn't for video tags), we use a temporary canvas
                let finalImageDataUrl = imageDataUrl;
                if (!finalImageDataUrl) {
                    const tempCanvas = document.createElement('canvas');
                    tempCanvas.width = this.videoElement.videoWidth;
                    tempCanvas.height = this.videoElement.videoHeight;
                    const ctx = tempCanvas.getContext('2d');
                    ctx.drawImage(this.videoElement, 0, 0);
                    finalImageDataUrl = tempCanvas.toDataURL('image/jpeg');
                }

                await this.activeOptions.onCapture(descriptor, this, finalImageDataUrl);
            } catch (error) {
                this.setStatus(this.getFriendlyErrorMessage(error), 'danger');
            } finally {
                this.setBusy(false);
            }
        }

        getFriendlyErrorMessage(error) {
            const rawMessage = (error?.message || '').toLowerCase();
            const errorName = (error?.name || '').toLowerCase();

            if (rawMessage.includes('requested device not found') || errorName === 'notfounderror') {
                return 'No camera was found on this device. Please connect or enable a camera and try again.';
            }

            if (rawMessage.includes('permission denied') || rawMessage.includes('permission dismissed') || errorName === 'notallowederror') {
                return 'Camera access was blocked. Please allow camera permission in your browser and try again.';
            }

            if (rawMessage.includes('could not start video source') || rawMessage.includes('device in use') || errorName === 'notreadableerror') {
                return 'Your camera is busy in another app or tab. Close other camera apps and try again.';
            }

            if (rawMessage.includes('overconstrained') || errorName === 'overconstrainederror') {
                return 'This camera setup is not supported on your device. Please try again with a different camera.';
            }

            if (rawMessage.includes('secure context') || rawMessage.includes('only secure origins') || rawMessage.includes('insecure')) {
                return 'Camera access needs a secure URL. Please open this page on localhost or HTTPS.';
            }

            if (rawMessage.includes('failed to load face recognition models') || rawMessage.includes('face-api.js failed to load')) {
                return 'Face login files could not be loaded. Please refresh the page and try again.';
            }

            if (rawMessage.includes('no face detected')) {
                return 'No face was detected. Please look straight at the camera and try again.';
            }

            return error?.message || 'Something went wrong while starting face login. Please try again.';
        }

        setBusy(isBusy) {
            this.captureButton.disabled = isBusy;
            this.captureButton.innerHTML = isBusy
                ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...'
                : (this.activeOptions?.captureLabel || 'Capture Face');
        }

        setStatus(message, tone = 'info') {
            const toneClass = {
                info: 'alert-light border',
                success: 'alert-success',
                danger: 'alert-danger',
                warning: 'alert-warning',
            }[tone] || 'alert-light border';

            this.statusElement.className = `alert mb-2 ${toneClass}`;
            this.statusElement.textContent = message;
        }

        showMatchInfo(message) {
            if (!message) {
                this.matchInfoElement.textContent = '';
                this.matchInfoElement.classList.add('d-none');
                return;
            }

            this.matchInfoElement.textContent = message;
            this.matchInfoElement.classList.remove('d-none');
        }

        close() {
            this.bootstrapModal.hide();
        }

        cleanup() {
            this.stopCamera();
            this.showMatchInfo('');
            this.captureButton.textContent = 'Capture Face';
            this.captureButton.disabled = false;
        }

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach((track) => track.stop());
                this.stream = null;
            }

            if (this.videoElement) {
                this.videoElement.pause();
                this.videoElement.srcObject = null;
            }
        }
    }

    window.OmsaiFaceRecognition = {
        instance: null,
        init(config = {}) {
            if (!this.instance) {
                this.instance = new FaceRecognitionModal(config);
            }

            return this.instance;
        }
    };
})(window, document);

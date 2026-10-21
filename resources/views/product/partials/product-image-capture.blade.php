<style>
    #productImageCameraModal .product-image-camera-video {
        width: 100%;
        max-height: 360px;
        border-radius: 8px;
        background: #000;
        object-fit: cover;
    }

    #productImageCameraModal .product-image-camera-actions {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
</style>

<div class="modal fade" id="productImageCameraModal" tabindex="-1" aria-labelledby="productImageCameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productImageCameraModalLabel">Capture Product Image</h5>
                <button type="button" class="btn-close bg-white text-black" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <video id="productImageCameraVideo" class="product-image-camera-video" autoplay playsinline muted></video>
                <canvas id="productImageCameraCanvas" style="display:none;"></canvas>
                <div id="productImageCameraStatus" class="text-center mt-2 small text-muted">Starting camera...</div>
                <div class="product-image-camera-actions">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="productImageCameraSwitchBtn" style="display:none;">
                        <i class="fa-solid fa-rotate"></i> Switch Camera
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="productImageCameraCaptureBtn">
                    <i class="fa-solid fa-camera me-1"></i> Capture
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';

    var stream = null;
    var useFrontCamera = false;
    var modalEl = document.getElementById('productImageCameraModal');
    var videoEl = document.getElementById('productImageCameraVideo');
    var canvasEl = document.getElementById('productImageCameraCanvas');
    var statusEl = document.getElementById('productImageCameraStatus');
    var switchBtn = document.getElementById('productImageCameraSwitchBtn');
    var captureBtn = document.getElementById('productImageCameraCaptureBtn');
    var openBtn = document.getElementById('openProductImageCamera');

    if (!modalEl || !videoEl || !canvasEl || !openBtn) {
        return;
    }

    function setStatus(message, isError) {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = message || '';
        statusEl.classList.toggle('text-danger', !!isError);
        statusEl.classList.toggle('text-muted', !isError);
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(function(track) {
                track.stop();
            });
            stream = null;
        }
        if (videoEl) {
            videoEl.srcObject = null;
        }
    }

    function getCameraConstraints() {
        return {
            audio: false,
            video: {
                facingMode: useFrontCamera ? 'user' : 'environment',
                // Request the highest resolution the device can provide (no forced downscale).
                width: { ideal: 4096 },
                height: { ideal: 4096 }
            }
        };
    }

    function startCamera() {
        stopCamera();
        setStatus('Starting camera...', false);
        if (captureBtn) {
            captureBtn.disabled = true;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setStatus('Camera is not supported on this browser. Please upload an image instead.', true);
            return;
        }

        navigator.mediaDevices.getUserMedia(getCameraConstraints()).then(function(mediaStream) {
            stream = mediaStream;
            videoEl.srcObject = stream;
            return videoEl.play();
        }).then(function() {
            if (captureBtn) {
                captureBtn.disabled = false;
            }
            if (switchBtn) {
                switchBtn.style.display = '';
            }
            setStatus('Position the product in frame and tap Capture.', false);
        }).catch(function() {
            if (!useFrontCamera) {
                useFrontCamera = true;
                startCamera();
                return;
            }
            setStatus('Could not access camera. Allow camera permission or upload an image.', true);
        });
    }

    function appendCapturedFile(file) {
        if (window.ProductImagesManager) {
            window.ProductImagesManager.addFiles([file]);
            return;
        }

        var input = document.getElementById('images');
        if (!input || !file) {
            return;
        }

        var generatedPath = document.getElementById('generated_image_path');
        if (generatedPath) {
            generatedPath.value = '';
        }

        var dataTransfer = new DataTransfer();
        Array.from(input.files || []).forEach(function(existingFile) {
            dataTransfer.items.add(existingFile);
        });
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    openBtn.addEventListener('click', function() {
        useFrontCamera = false;
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
            window.jQuery(modalEl).modal('show');
        }
    });

    modalEl.addEventListener('shown.bs.modal', startCamera);
    modalEl.addEventListener('hidden.bs.modal', function() {
        stopCamera();
        useFrontCamera = false;
        if (switchBtn) {
            switchBtn.style.display = 'none';
        }
    });

    if (switchBtn) {
        switchBtn.addEventListener('click', function() {
            useFrontCamera = !useFrontCamera;
            startCamera();
        });
    }

    if (captureBtn) {
        captureBtn.addEventListener('click', function() {
            if (!stream || !videoEl.videoWidth || !videoEl.videoHeight) {
                setStatus('Camera is not ready yet. Please wait a moment.', true);
                return;
            }

            canvasEl.width = videoEl.videoWidth;
            canvasEl.height = videoEl.videoHeight;
            canvasEl.getContext('2d').drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);

            // Prefer lossless PNG at full camera resolution; fall back to max-quality JPEG.
            function finishCapture(blob, ext, mime) {
                if (!blob) {
                    setStatus('Could not capture image. Please try again.', true);
                    return;
                }

                var fileName = 'product-capture-' + Date.now() + '.' + ext;
                var file = new File([blob], fileName, { type: mime });
                appendCapturedFile(file);

                if (window.bootstrap && bootstrap.Modal) {
                    var instance = bootstrap.Modal.getInstance(modalEl);
                    if (instance) {
                        instance.hide();
                    }
                } else if (window.jQuery) {
                    window.jQuery(modalEl).modal('hide');
                }

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'success',
                        title: 'Image captured',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            }

            if (canvasEl.toBlob) {
                canvasEl.toBlob(function(pngBlob) {
                    if (pngBlob) {
                        finishCapture(pngBlob, 'png', 'image/png');
                        return;
                    }
                    canvasEl.toBlob(function(jpgBlob) {
                        finishCapture(jpgBlob, 'jpg', 'image/jpeg');
                    }, 'image/jpeg', 1.0);
                }, 'image/png');
            }
        });
    }
})();
</script>

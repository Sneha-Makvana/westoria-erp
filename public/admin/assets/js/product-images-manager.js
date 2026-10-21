(function(window, document) {
    'use strict';

    var manager = {
        pendingFiles: [],
        generatedPaths: [],
        imagePath: '',
        previewEl: null,
        inputEl: null,
        errorEl: null,
        genSingleEl: null,
        genMultiEl: null,

        init: function(options) {
            options = options || {};
            this.imagePath = (options.imagePath || '').replace(/\/$/, '');
            this.previewEl = document.querySelector(options.previewSelector || '.image-preview');
            this.inputEl = document.getElementById(options.inputId || 'images');
            this.errorEl = document.querySelector(options.errorSelector || '.error_images');
            this.genSingleEl = document.getElementById('generated_image_path');
            this.genMultiEl = document.getElementById('generated_image_paths');
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;
            if (!this.inputEl) {
                return;
            }

            this.inputEl.addEventListener('change', function(event) {
                var files = Array.from(event.target.files || []);
                if (!files.length) {
                    return;
                }
                self.addFiles(files);
            });
        },

        syncInput: function() {
            if (!this.inputEl) {
                return;
            }
            var dataTransfer = new DataTransfer();
            this.pendingFiles.forEach(function(file) {
                dataTransfer.items.add(file);
            });
            this.inputEl.files = dataTransfer.files;
        },

        syncGeneratedPaths: function() {
            if (this.genMultiEl) {
                this.genMultiEl.value = JSON.stringify(this.generatedPaths);
            }
            if (this.genSingleEl) {
                this.genSingleEl.value = this.generatedPaths.length
                    ? this.generatedPaths[this.generatedPaths.length - 1]
                    : '';
            }
        },

        clearGeneratedPaths: function() {
            this.generatedPaths = [];
            this.syncGeneratedPaths();
        },

        addFiles: function(files) {
            var self = this;
            var list = Array.from(files || []);
            var validFiles = list.filter(function(file) {
                return file && file.type && file.type.indexOf('image/') === 0;
            });

            if (!validFiles.length) {
                return;
            }

            if (validFiles.length !== list.length && this.errorEl) {
                this.errorEl.textContent = 'Only image files are allowed.';
            } else if (this.errorEl) {
                this.errorEl.textContent = '';
            }

            validFiles.forEach(function(file) {
                var alreadyAdded = self.pendingFiles.some(function(existingFile) {
                    return existingFile.name === file.name
                        && existingFile.size === file.size
                        && existingFile.lastModified === file.lastModified;
                });
                if (alreadyAdded) {
                    return;
                }
                self.pendingFiles.push(file);
                self.renderPendingPreview(file, self.pendingFiles.length - 1);
            });

            this.syncInput();
        },

        removePending: function(index) {
            if (index < 0 || index >= this.pendingFiles.length) {
                return;
            }
            this.pendingFiles.splice(index, 1);
            this.syncInput();
            this.rerenderPendingPreviews();
        },

        rerenderPendingPreviews: function() {
            var self = this;
            if (!this.previewEl) {
                return;
            }
            this.previewEl.querySelectorAll('[data-pending-index]').forEach(function(node) {
                node.remove();
            });
            this.pendingFiles.forEach(function(file, index) {
                self.renderPendingPreview(file, index);
            });
        },

        renderPendingPreview: function(file, index) {
            var self = this;
            if (!this.previewEl || !file) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function(event) {
                var container = document.createElement('div');
                container.className = 'image-container image-container-pending';
                container.setAttribute('data-pending-index', String(index));
                container.style.cssText = 'position:relative;display:inline-block;margin:5px;vertical-align:top;';

                var img = document.createElement('img');
                img.src = event.target.result;
                img.alt = 'Preview';
                // Preview only — uploaded file stays original size/quality.
                img.style.cssText = 'max-width:180px;max-height:180px;width:auto;height:auto;object-fit:contain;border-radius:5px;box-shadow:2px 2px 10px rgba(0,0,0,0.1);background:#f8f9fa;';
                img.title = (file.name || 'image') + ' (' + Math.round((file.size || 0) / 1024) + ' KB)';

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '&times;';
                removeBtn.style.cssText = 'position:absolute;top:-6px;right:-6px;background:red;color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;';
                removeBtn.addEventListener('click', function() {
                    var currentIndex = self.pendingFiles.indexOf(file);
                    if (currentIndex >= 0) {
                        self.removePending(currentIndex);
                    }
                });

                var removeBgWrapper = document.createElement('div');
                removeBgWrapper.style.marginTop = '4px';

                var removeBgBtn = document.createElement('button');
                removeBgBtn.type = 'button';
                removeBgBtn.className = 'btn btn-sm btn-outline-success';
                removeBgBtn.innerHTML = '<i class="fas fa-eraser"></i> Remove BG';
                removeBgBtn.style.fontSize = '11px';
                removeBgBtn.style.padding = '2px 7px';
                removeBgBtn.addEventListener('click', function() {
                    removeBgBtn.disabled = true;
                    removeBgBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';

                    var formData = new FormData();
                    formData.append('image_file', file);

                    if (window.jQuery) {
                        window.jQuery.ajax({
                            url: '/api/products/remove-background',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                Authorization: 'Bearer ' + (localStorage.getItem('authToken') || '')
                            },
                            success: function(response) {
                                if (response.success && response.image_path) {
                                    var pendingIndex = self.pendingFiles.indexOf(file);
                                    if (pendingIndex >= 0) {
                                        self.removePending(pendingIndex);
                                    }
                                    self.appendGeneratedImage(response.image_path);
                                } else {
                                    removeBgBtn.disabled = false;
                                    removeBgBtn.innerHTML = '<i class="fas fa-eraser"></i> Remove BG';
                                    if (window.Swal) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Remove BG failed',
                                            text: (response && response.message) || 'Could not remove background.',
                                            confirmButtonColor: '#ff9f43'
                                        });
                                    }
                                }
                            },
                            error: function(xhr) {
                                removeBgBtn.disabled = false;
                                removeBgBtn.innerHTML = '<i class="fas fa-eraser"></i> Remove BG';
                                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                    ? xhr.responseJSON.message
                                    : 'Background removal request failed.';
                                if (window.Swal) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Remove BG failed',
                                        text: msg,
                                        confirmButtonColor: '#ff9f43'
                                    });
                                }
                            }
                        });
                    }
                });

                removeBgWrapper.appendChild(removeBgBtn);
                container.appendChild(img);
                container.appendChild(removeBtn);
                container.appendChild(removeBgWrapper);
                self.previewEl.appendChild(container);
            };
            reader.readAsDataURL(file);
        },

        renderServerImages: function(images, productId) {
            var self = this;
            if (!this.previewEl || !Array.isArray(images)) {
                return;
            }

            images.forEach(function(imagePath) {
                if (!imagePath) {
                    return;
                }
                self.appendServerImage(imagePath, productId);
            });
        },

        appendServerImage: function(imagePath, productId) {
            if (!this.previewEl || !imagePath) {
                return;
            }

            var container = document.createElement('div');
            container.className = 'image-container image-container-server';
            container.setAttribute('data-server-image', imagePath);
            container.style.cssText = 'position:relative;display:inline-block;margin:5px;vertical-align:top;';

            var img = document.createElement('img');
            img.src = this.imagePath + '/storage/' + imagePath;
            img.style.cssText = 'max-width:180px;max-height:180px;width:auto;height:auto;object-fit:contain;border-radius:5px;background:#f8f9fa;';

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image';
            removeBtn.innerHTML = '&times;';
            if (productId) {
                removeBtn.setAttribute('data-id', String(productId));
            }
            removeBtn.setAttribute('data-image', imagePath);
            removeBtn.style.cssText = 'position:absolute;top:-6px;right:-6px;background:red;color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;';

            container.appendChild(img);
            container.appendChild(removeBtn);
            this.previewEl.appendChild(container);
        },

        appendGeneratedImage: function(imagePath, options) {
            options = options || {};
            if (!imagePath || this.generatedPaths.indexOf(imagePath) >= 0) {
                return;
            }

            this.generatedPaths.push(imagePath);
            this.syncGeneratedPaths();

            if (!this.previewEl) {
                return;
            }

            var container = document.createElement('div');
            container.className = 'image-container image-container-generated';
            container.setAttribute('data-generated-path', imagePath);
            container.style.cssText = 'position:relative;display:inline-block;margin:5px;vertical-align:top;';

            var img = document.createElement('img');
            img.src = this.imagePath + '/storage/' + imagePath + '?t=' + Date.now();
            img.style.cssText = 'max-width:180px;max-height:180px;width:auto;height:auto;object-fit:contain;border-radius:5px;box-shadow:2px 2px 10px rgba(0,0,0,0.1);background:#f8f9fa;';

            if (options.showAiBadge) {
                var badge = document.createElement('span');
                badge.className = 'badge bg-primary';
                badge.textContent = 'AI';
                badge.style.cssText = 'position:absolute;bottom:5px;left:5px;font-size:10px;';
                container.appendChild(badge);
            }

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.style.cssText = 'position:absolute;top:-6px;right:-6px;background:red;color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:14px;line-height:1;';
            removeBtn.addEventListener('click', function() {
                manager.removeGeneratedImage(imagePath);
            });

            container.appendChild(img);
            container.appendChild(removeBtn);
            this.previewEl.appendChild(container);
        },

        removeGeneratedImage: function(imagePath) {
            this.generatedPaths = this.generatedPaths.filter(function(path) {
                return path !== imagePath;
            });
            this.syncGeneratedPaths();

            if (!this.previewEl) {
                return;
            }

            this.previewEl.querySelectorAll('[data-generated-path]').forEach(function(node) {
                if (node.getAttribute('data-generated-path') === imagePath) {
                    node.remove();
                }
            });
        },

        clearPending: function() {
            this.pendingFiles = [];
            this.syncInput();
            if (!this.previewEl) {
                return;
            }
            this.previewEl.querySelectorAll('[data-pending-index]').forEach(function(node) {
                node.remove();
            });
        },

        prepareForSubmit: function() {
            this.syncInput();
            this.syncGeneratedPaths();
        }
    };

    window.ProductImagesManager = manager;
})(window, document);

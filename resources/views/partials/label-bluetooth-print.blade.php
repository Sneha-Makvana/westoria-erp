<script src="{{ asset('js/qz-tray.js') }}?v=20260918"></script>
<script>
(function() {
    if (window.qz) return;
    var urls = [
        'https://cdn.jsdelivr.net/npm/qz-tray@2.2.5/qz-tray.js',
        'https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.2.4/qz-tray.js'
    ];
    var i = 0;
    function next() {
        if (window.qz || i >= urls.length) return;
        var s = document.createElement('script');
        s.src = urls[i++];
        s.onerror = next;
        document.head.appendChild(s);
    }
    next();
})();
</script>
<script>
(function() {
    if (window.LabelBluetoothPrint) return;

    var BT_SERVICES = [
        '000018f0-0000-1000-8000-00805f9b34fb',
        '0000ff00-0000-1000-8000-00805f9b34fb',
        '0000fee7-0000-1000-8000-00805f9b34fb',
        '0000ae30-0000-1000-8000-00805f9b34fb',
        '49535343-fe7d-4ae5-8fa9-9fafd205e455',
        'e7810a71-73ae-499d-8c15-faa9aef0c3f2'
    ];
    var DOTS_PER_MM = 8;
    var ESCPOS_MAX_DOTS = 576;
    var TSPL_MAX_MM = 104;
    var HTML2CANVAS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    var TARGET_KEY = 'labelPrintTarget';
    var DEVICES_KEY = 'labelBtDevices';
    var LANG_KEY = 'labelBtLanguage';
    var QZ_PREFIX = 'qz:';
    var QZ_CERT_URL = @json(url('/qz/certificate'));
    var QZ_SIGN_URL = @json(url('/qz/sign'));
    var LABEL_PRINTER_RE = /tvs|lp\s*46|dlite|tsc|te244|label|godex|zebra|argox/i;
    var devices = {};
    var els = {};
    var qzPrinters = [];
    var qzState = { security: false, connecting: null };

    function isIos() {
        var ua = navigator.userAgent || '';
        return /iPhone|iPad|iPod/i.test(ua) || (/Macintosh/i.test(ua) && navigator.maxTouchPoints > 1);
    }

    function isAndroid() {
        if (isIos()) return false;
        var ua = navigator.userAgent || '';
        if (/Android/i.test(ua)) return true;
        if (navigator.userAgentData && navigator.userAgentData.platform === 'Android') return true;
        return navigator.maxTouchPoints > 0 && window.innerWidth <= 900 && !/Windows|Macintosh|Linux x86/i.test(ua);
    }

    function hasWebBluetooth() {
        return !!(navigator.bluetooth && typeof navigator.bluetooth.requestDevice === 'function');
    }

    function setStatus(msg, isError) {
        if (!els.status) return;
        els.status.textContent = msg || '';
        els.status.className = (msg ? 'd-block' : 'd-none') + ' mt-1 small ' + (isError ? 'text-danger' : 'text-muted');
    }

    function savedDevices() {
        try { return JSON.parse(localStorage.getItem(DEVICES_KEY) || '{}') || {}; } catch (e) { return {}; }
    }

    function rememberDevice(id, name) {
        var all = savedDevices();
        all[id] = name;
        try { localStorage.setItem(DEVICES_KEY, JSON.stringify(all)); } catch (e) {}
    }

    function addOption(value, label) {
        var sel = els.select;
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value === value) {
                sel.options[i].textContent = label;
                return;
            }
        }
        var opt = document.createElement('option');
        opt.value = value;
        opt.textContent = label;
        sel.appendChild(opt);
    }

    function fillSelect() {
        var sel = els.select;
        sel.innerHTML = '';
        addOption('browser', 'Print dialog (choose in browser)');
        qzPrinters.forEach(function(name) {
            addOption(QZ_PREFIX + name, name);
        });
        if (hasWebBluetooth()) {
            var all = savedDevices();
            Object.keys(all).forEach(function(id) {
                addOption('bt:' + id, all[id] + ' (Bluetooth)');
            });
        }
        var stored = '';
        try { stored = localStorage.getItem(TARGET_KEY) || ''; } catch (e) {}
        // Never default to RawBT app.
        if (stored === 'bt:phone') {
            try { localStorage.removeItem(TARGET_KEY); } catch (eClr) {}
            stored = '';
        }
        if (!stored) {
            for (var i = 0; i < qzPrinters.length; i++) {
                if (LABEL_PRINTER_RE.test(qzPrinters[i])) {
                    stored = QZ_PREFIX + qzPrinters[i];
                    break;
                }
            }
        }
        sel.value = stored;
        if (sel.value !== stored) sel.value = 'browser';
        syncUi();
    }

    function syncUi() {
        var t = target();
        var bt = t.indexOf('bt:') === 0;
        if (els.langWrap) els.langWrap.classList.toggle('d-none', !bt);
        if (isIos() && !hasWebBluetooth()) {
            setStatus('iPhone cannot send to a Bluetooth label printer from a website. Use the print dialog.', false);
        } else if (bt) {
            setStatus('Labels go straight to the Bluetooth printer.', false);
        } else if (t) {
            setStatus('Prints straight to ' + t.slice(QZ_PREFIX.length) + ', no print window.', false);
        } else {
            setStatus('', false);
        }
    }

    function target() {
        var v = els.select ? String(els.select.value || '') : '';
        return (v.indexOf('bt:') === 0 || v.indexOf(QZ_PREFIX) === 0) ? v : '';
    }

    function waitForQz(ms) {
        return new Promise(function(resolve, reject) {
            var started = Date.now();
            (function poll() {
                if (window.qz && window.qz.websocket) return resolve(window.qz);
                if (Date.now() - started > ms) return reject(new Error('QZ Tray is not loaded'));
                setTimeout(poll, 150);
            })();
        });
    }

    function setupQzSecurity(qz) {
        if (qzState.security) return;
        qz.security.setCertificatePromise(function(resolve, reject) {
            fetch(QZ_CERT_URL, {
                cache: 'no-store',
                credentials: 'same-origin',
                headers: { 'Accept': 'text/plain' }
            }).then(function(res) {
                if (!res.ok) throw new Error('Certificate HTTP ' + res.status);
                return res.text();
            }).then(resolve).catch(reject);
        });
        if (typeof qz.security.setSignatureAlgorithm === 'function') {
            qz.security.setSignatureAlgorithm('SHA512');
        }
        qz.security.setSignaturePromise(function(toSign) {
            return function(resolve, reject) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var csrf = meta ? (meta.getAttribute('content') || '') : '';
                fetch(QZ_SIGN_URL, {
                    method: 'POST',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Accept': 'text/plain',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: 'request=' + encodeURIComponent(toSign) + (csrf ? ('&_token=' + encodeURIComponent(csrf)) : '')
                }).then(function(res) {
                    if (!res.ok) throw new Error('Sign HTTP ' + res.status);
                    return res.text();
                }).then(resolve).catch(reject);
            };
        });
        qzState.security = true;
    }

    function ensureQz() {
        return waitForQz(6000).then(function(qz) {
            setupQzSecurity(qz);
            if (qz.websocket.isActive()) return qz;
            if (!qzState.connecting) {
                qzState.connecting = qz.websocket.connect({ retries: 2, delay: 1 }).then(function() {
                    qzState.connecting = null;
                }, function(err) {
                    qzState.connecting = null;
                    throw err;
                });
            }
            return qzState.connecting.then(function() { return qz; });
        });
    }

    function refreshPrinters() {
        if (!els.select || isAndroid() || isIos()) return Promise.resolve([]);
        return ensureQz().then(function(qz) {
            return qz.printers.find();
        }).then(function(list) {
            qzPrinters = (list || []).filter(Boolean);
            fillSelect();
            return qzPrinters;
        }).catch(function(err) {
            console.warn('QZ printers', err);
            if (!target()) {
                setStatus('Start QZ Tray on this computer to print straight to USB printers.', false);
            }
            return [];
        });
    }

    function qzPrint(canvases, pageWidthMm, pageHeightMm) {
        var name = target().slice(QZ_PREFIX.length);
        setStatus('Printing labels on ' + name + '…', false);
        return ensureQz().then(function(qz) {
            var config = qz.configs.create(name, {
                size: { width: pageWidthMm, height: pageHeightMm },
                units: 'mm',
                margins: 0,
                scaleContent: true,
                interpolation: 'nearest-neighbor'
            });
            var data = canvases.map(function(canvas) {
                return {
                    type: 'pixel',
                    format: 'image',
                    flavor: 'base64',
                    data: canvas.toDataURL('image/png').replace(/^data:image\/png;base64,/, '')
                };
            });
            return qz.print(config, data);
        }).then(function() {
            setStatus('Printed', false);
        }).catch(function(err) {
            setStatus((err && err.message) ? err.message : 'Print failed', true);
            throw err;
        });
    }

    function language() {
        if (target().indexOf(QZ_PREFIX) === 0) return 'tspl';
        return els.lang && els.lang.value === 'escpos' ? 'escpos' : 'tspl';
    }

    function isQzLabelPrinter() {
        var t = target();
        return t.indexOf(QZ_PREFIX) === 0 && LABEL_PRINTER_RE.test(t.slice(QZ_PREFIX.length));
    }

    function chooseDevice() {
        if (!hasWebBluetooth()) {
            setStatus('Use Chrome on this computer to print by Bluetooth.', true);
            return Promise.resolve(null);
        }
        setStatus('Choose a nearby printer', false);
        return navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: BT_SERVICES
        }).then(function(device) {
            var name = device.name || 'Bluetooth printer';
            devices[device.id] = device;
            rememberDevice(device.id, name);
            addOption('bt:' + device.id, name + ' (Bluetooth)');
            els.select.value = 'bt:' + device.id;
            try { localStorage.setItem(TARGET_KEY, els.select.value); } catch (e) {}
            syncUi();
            setStatus('Selected ' + name + '. Press Print Barcode.', false);
            return device;
        }).catch(function(err) {
            if (!(err && (err.name === 'NotFoundError' || err.name === 'AbortError'))) {
                setStatus((err && err.message) ? err.message : 'Could not open Bluetooth', true);
            }
            return null;
        });
    }

    function findDevice(id) {
        if (devices[id]) return Promise.resolve(devices[id]);
        if (navigator.bluetooth && navigator.bluetooth.getDevices) {
            return navigator.bluetooth.getDevices().then(function(list) {
                for (var i = 0; i < (list || []).length; i++) {
                    if (list[i].id === id) {
                        devices[id] = list[i];
                        return list[i];
                    }
                }
                throw new Error('Printer not found. Press Bluetooth and choose it again.');
            });
        }
        return Promise.reject(new Error('Printer not found. Press Bluetooth and choose it again.'));
    }

    function findWritableCharacteristic(server) {
        var i = 0;
        function next() {
            if (i >= BT_SERVICES.length) return Promise.resolve(null);
            var uuid = BT_SERVICES[i++];
            return server.getPrimaryService(uuid).then(function(service) {
                return service.getCharacteristics();
            }).then(function(chars) {
                for (var c = 0; c < chars.length; c++) {
                    var props = chars[c].properties || {};
                    if (props.write || props.writeWithoutResponse) return chars[c];
                }
                return next();
            }).catch(function() {
                return next();
            });
        }
        return next();
    }

    function writePrinterBytes(characteristic, bytes) {
        function writePart(part) {
            var props = characteristic.properties || {};
            var write = (props.writeWithoutResponse && characteristic.writeValueWithoutResponse)
                ? characteristic.writeValueWithoutResponse(part)
                : characteristic.writeValue(part);
            return Promise.resolve(write).catch(function() {
                if (part.length <= 20) {
                    throw new Error('The printer rejected the labels');
                }
                var mid = Math.ceil(part.length / 2);
                return writePart(part.slice(0, mid)).then(function() {
                    return writePart(part.slice(mid));
                });
            });
        }
        var size = 100;
        var offset = 0;
        function next() {
            if (offset >= bytes.length) return Promise.resolve();
            var part = bytes.slice(offset, offset + size);
            offset += size;
            return writePart(part).then(function() {
                return new Promise(function(resolve) { setTimeout(resolve, 15); });
            }).then(next);
        }
        return next();
    }

    function bytesToBase64(bytes) {
        var text = '';
        for (var i = 0; i < bytes.length; i++) text += String.fromCharCode(bytes[i]);
        return btoa(text);
    }

    function openRawBt(bytes) {
        var b64 = bytesToBase64(bytes).replace(/\+/g, '%2B').replace(/\//g, '%2F');
        var fallback = 'https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter';
        window.location.href = 'intent:base64,' + b64 + '#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;S.browser_fallback_url=' + encodeURIComponent(fallback) + ';end;';
    }

    function loadImage(src) {
        return new Promise(function(resolve, reject) {
            var img = new Image();
            img.onload = function() { resolve(img); };
            img.onerror = function() { reject(new Error('Could not load the label image')); };
            img.src = src;
        });
    }

    function whiteCanvas(w, h) {
        var canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(w));
        canvas.height = Math.max(1, Math.round(h));
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        return canvas;
    }

    function tsplWidthMm(widthMm) {
        return Math.min(widthMm, TSPL_MAX_MM);
    }

    function fitCanvas(source, widthMm, heightMm) {
        var w = Math.round(widthMm * DOTS_PER_MM);
        var h = Math.round(heightMm * DOTS_PER_MM);
        if (language() === 'tspl') {
            w = Math.round(tsplWidthMm(widthMm) * DOTS_PER_MM);
        }
        if (language() === 'escpos' && w > ESCPOS_MAX_DOTS) {
            h = Math.round(h * ESCPOS_MAX_DOTS / w);
            w = ESCPOS_MAX_DOTS;
        }
        var canvas = whiteCanvas(w, h);
        var ctx = canvas.getContext('2d');
        ctx.imageSmoothingEnabled = true;
        ctx.drawImage(source, 0, 0, w, h);
        return canvas;
    }

    function blackMap(canvas) {
        var data = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
        var map = new Uint8Array(canvas.width * canvas.height);
        for (var i = 0, p = 0; i < data.length; i += 4, p++) {
            var a = data[i + 3] / 255;
            var lum = (0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2]) * a + 255 * (1 - a);
            map[p] = lum < 185 ? 1 : 0;
        }
        return map;
    }

    function ascii(out, text) {
        for (var i = 0; i < text.length; i++) out.push(text.charCodeAt(i) & 0xFF);
    }

    function tsplLabel(out, canvas, widthMm, heightMm, count) {
        var w = canvas.width;
        var h = canvas.height;
        var wBytes = Math.ceil(w / 8);
        var map = blackMap(canvas);
        ascii(out, 'SIZE ' + tsplWidthMm(widthMm) + ' mm, ' + heightMm + ' mm\r\nCLS\r\nBITMAP 0,0,' + wBytes + ',' + h + ',0,');
        for (var y = 0; y < h; y++) {
            for (var xb = 0; xb < wBytes; xb++) {
                var byte = 0xFF;
                for (var bit = 0; bit < 8; bit++) {
                    var x = xb * 8 + bit;
                    if (x < w && map[y * w + x]) byte &= ~(0x80 >> bit);
                }
                out.push(byte);
            }
        }
        ascii(out, '\r\nPRINT 1,' + Math.max(1, count) + '\r\n');
    }

    function escposLabel(out, canvas, count) {
        var w = canvas.width;
        var h = canvas.height;
        var wBytes = Math.ceil(w / 8);
        var map = blackMap(canvas);
        var block = [];
        for (var top = 0; top < h; top += 200) {
            var rows = Math.min(200, h - top);
            block.push(0x1D, 0x76, 0x30, 0x00, wBytes & 0xFF, (wBytes >> 8) & 0xFF, rows & 0xFF, (rows >> 8) & 0xFF);
            for (var y = top; y < top + rows; y++) {
                for (var xb = 0; xb < wBytes; xb++) {
                    var byte = 0;
                    for (var bit = 0; bit < 8; bit++) {
                        var x = xb * 8 + bit;
                        if (x < w && map[y * w + x]) byte |= (0x80 >> bit);
                    }
                    block.push(byte);
                }
            }
        }
        block.push(0x1B, 0x64, 0x02);
        for (var c = 0; c < Math.max(1, count); c++) {
            for (var i = 0; i < block.length; i++) out.push(block[i]);
        }
    }

    // pages: [{ canvas, key, widthMm, heightMm }] in print order
    function buildBytes(pages) {
        var out = [];
        if (language() === 'escpos') out.push(0x1B, 0x40);
        var i = 0;
        while (i < pages.length) {
            var count = 1;
            while (i + count < pages.length && pages[i + count].key && pages[i + count].key === pages[i].key) count++;
            if (language() === 'escpos') {
                escposLabel(out, pages[i].canvas, count);
            } else {
                tsplLabel(out, pages[i].canvas, pages[i].widthMm, pages[i].heightMm, count);
            }
            i += count;
        }
        return new Uint8Array(out);
    }

    function send(bytes) {
        var t = target();
        if (!t) return Promise.reject(new Error('Choose a Bluetooth printer first'));
        if (t === 'bt:phone') {
            return Promise.reject(new Error('Choose a nearby Bluetooth printer (RawBT is disabled).'));
        }
        var id = t.slice(3);
        var device = null;
        setStatus('Printing labels by Bluetooth…', false);
        return findDevice(id).then(function(found) {
            device = found;
            return device.gatt.connect();
        }).then(function(server) {
            return findWritableCharacteristic(server);
        }).then(function(characteristic) {
            if (!characteristic) {
                throw new Error('This Bluetooth device is not a printer the browser can print to');
            }
            return writePrinterBytes(characteristic, bytes);
        }).then(function() {
            try { device.gatt.disconnect(); } catch (e) {}
            setStatus('Printed', false);
        }).catch(function(err) {
            try { if (device && device.gatt) device.gatt.disconnect(); } catch (e2) {}
            setStatus((err && err.message) ? err.message : 'Bluetooth print failed', true);
            throw err;
        });
    }

    // canvases are full-resolution label rows; keys mark identical rows
    function deliver(canvases, keys, pageWidthMm, pageHeightMm) {
        if (target().indexOf(QZ_PREFIX) === 0 && !isQzLabelPrinter()) {
            return qzPrint(canvases, pageWidthMm, pageHeightMm);
        }
        var fitted = {};
        var bytes = buildBytes(canvases.map(function(canvas, i) {
            var key = keys[i];
            if (!fitted[key]) {
                fitted[key] = fitCanvas(canvas, pageWidthMm, pageHeightMm);
                sharpenBarcodes(fitted[key], canvas.__barcodes);
            }
            return { canvas: fitted[key], key: key, widthMm: pageWidthMm, heightMm: pageHeightMm };
        }));
        return isQzLabelPrinter() ? qzPrintRaw(bytes) : send(bytes);
    }

    function qzPrintRaw(bytes) {
        var name = target().slice(QZ_PREFIX.length);
        setStatus('Printing labels on ' + name + '…', false);
        return ensureQz().then(function(qz) {
            return qz.print(qz.configs.create(name), [{
                type: 'raw',
                format: 'base64',
                data: bytesToBase64(bytes)
            }]);
        }).then(function() {
            setStatus('Printed', false);
        }).catch(function(err) {
            setStatus((err && err.message) ? err.message : 'Print failed', true);
            throw err;
        });
    }

    function printRows(rowImages, pageWidthMm, pageHeightMm) {
        var list = (rowImages || []).filter(Boolean);
        if (!list.length) return Promise.reject(new Error('No labels to print'));
        var cache = {};
        return Promise.all(list.map(function(src) {
            if (!cache[src]) {
                cache[src] = loadImage(src).then(function(img) {
                    var canvas = whiteCanvas(img.naturalWidth || img.width, img.naturalHeight || img.height);
                    canvas.getContext('2d').drawImage(img, 0, 0);
                    return canvas;
                });
            }
            return cache[src];
        })).then(function(canvases) {
            return deliver(canvases, list, pageWidthMm, pageHeightMm);
        });
    }

    // Reads bar/space widths (in modules) from a crisp barcode image
    function decodeBars(src) {
        return loadImage(src).then(function(img) {
            var nw = img.naturalWidth || img.width;
            var nh = img.naturalHeight || img.height;
            if (!nw || !nh) return null;
            var k = Math.max(1, Math.ceil(1600 / nw));
            var W = nw * k;
            var H = nh * k;
            var canvas = whiteCanvas(W, H);
            var ctx = canvas.getContext('2d');
            ctx.imageSmoothingEnabled = false;
            ctx.drawImage(img, 0, 0, W, H);
            var data = ctx.getImageData(0, 0, W, H).data;
            function dark(x, y) {
                var i = (y * W + x) * 4;
                var a = data[i + 3] / 255;
                return (0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2]) * a + 255 * (1 - a) < 128;
            }
            var y = Math.round(H * 0.4);
            var x0 = -1;
            var x1 = -1;
            for (var x = 0; x < W; x++) {
                if (dark(x, y)) {
                    if (x0 < 0) x0 = x;
                    x1 = x;
                }
            }
            if (x0 < 0) return null;
            var runs = [];
            var cur = true;
            var len = 0;
            for (x = x0; x <= x1; x++) {
                var d = dark(x, y);
                if (d === cur) {
                    len++;
                } else {
                    runs.push(len);
                    cur = d;
                    len = 1;
                }
            }
            runs.push(len);
            if (runs.length < 20) return null;
            var min = Math.min.apply(null, runs);
            var near = runs.filter(function(r) { return r <= min * 1.5; });
            var module = near.reduce(function(a, b) { return a + b; }, 0) / near.length;
            if (module / k < 1.8) return null;
            var modules = [];
            var total = 0;
            for (var i = 0; i < runs.length; i++) {
                var m = runs[i] / module;
                var rm = Math.round(m);
                if (rm < 1 || rm > 4 || Math.abs(m - rm) > 0.4) return null;
                modules.push(rm);
                total += rm;
            }
            var cx = x0 + Math.floor(runs[0] / 2);
            var top = y;
            var bottom = y;
            while (top > 0 && dark(cx, top - 1)) top--;
            while (bottom < H - 1 && dark(cx, bottom + 1)) bottom++;
            return {
                modules: modules,
                total: total,
                l: x0 / W,
                r: (x1 + 1) / W,
                t: top / H,
                b: (bottom + 1) / H
            };
        }).catch(function() {
            return null;
        });
    }

    // Exact module pattern from a '1'/'0' bit string, laid out with `quiet` blank modules each side
    function bitsInfo(bits, quiet) {
        bits = String(bits || '').replace(/[^01]/g, '');
        if (bits.length < 20 || bits.charAt(0) !== '1') return null;
        var modules = [];
        var len = 1;
        for (var i = 1; i < bits.length; i++) {
            if (bits.charAt(i) === bits.charAt(i - 1)) {
                len++;
            } else {
                modules.push(len);
                len = 1;
            }
        }
        modules.push(len);
        var last = bits.lastIndexOf('1');
        var total = last + 1;
        if (bits.charAt(bits.length - 1) === '0') modules.pop();
        var slots = bits.length + quiet * 2;
        return {
            modules: modules,
            total: total,
            l: quiet / slots,
            r: (quiet + total) / slots,
            t: 0,
            b: 1
        };
    }

    function isTransformed(el, stop) {
        var win = el.ownerDocument.defaultView;
        while (el && el !== stop) {
            var tf = win.getComputedStyle(el).transform;
            if (tf && tf !== 'none') return true;
            el = el.parentElement;
        }
        return false;
    }

    function collectBarcodes(rowEl) {
        var rowRect = rowEl.getBoundingClientRect();
        if (!rowRect.width || !rowRect.height) return Promise.resolve([]);
        var imgs = Array.prototype.slice.call(rowEl.querySelectorAll('.bc-wrap img, .lbl-bc:not(.lbl-text) img'));
        return Promise.all(imgs.map(function(img) {
            if (!img.src || isTransformed(img, rowEl)) return null;
            var r = img.getBoundingClientRect();
            var bits = img.getAttribute('data-bits');
            var infoReady = bits
                ? Promise.resolve(bitsInfo(bits, parseInt(img.getAttribute('data-quiet'), 10) || 0))
                : decodeBars(img.src);
            return infoReady.then(function(info) {
                if (!info) return null;
                return {
                    fx: (r.left - rowRect.left) / rowRect.width,
                    fy: (r.top - rowRect.top) / rowRect.height,
                    fw: r.width / rowRect.width,
                    fh: r.height / rowRect.height,
                    info: info
                };
            });
        })).then(function(list) {
            return list.filter(Boolean);
        });
    }

    // Redraws barcodes on the printer-dot canvas so every bar is a whole number of dots wide
    function sharpenBarcodes(canvas, list) {
        if (!list || !list.length) return;
        var ctx = canvas.getContext('2d');
        var W = canvas.width;
        var H = canvas.height;
        list.forEach(function(bc) {
            var info = bc.info;
            var boxL = bc.fx * W;
            var boxW = bc.fw * W;
            var boxT = bc.fy * H;
            var boxH = bc.fh * H;
            var barL = boxL + info.l * boxW;
            var barR = boxL + info.r * boxW;
            var top = Math.floor(boxT + info.t * boxH);
            var bottom = Math.ceil(boxT + info.b * boxH);
            if (bottom <= top) return;
            var maxW = boxW * 1.06;
            var dpm = Math.max(1, Math.round((barR - barL) / info.total));
            while (dpm > 1 && dpm * info.total > maxW) dpm--;
            var drawW = dpm * info.total;
            if (drawW > maxW) return;
            var start = Math.round((barL + barR) / 2 - drawW / 2);
            var clearL = Math.min(start, Math.floor(boxL) - 1);
            var clearR = Math.max(start + drawW, Math.ceil(boxL + boxW) + 1);
            var clearT = Math.min(top, Math.floor(boxT) - 1);
            var clearB = Math.max(bottom, Math.ceil(boxT + boxH) + 1);
            ctx.fillStyle = '#fff';
            ctx.fillRect(clearL, clearT, clearR - clearL, clearB - clearT);
            ctx.fillStyle = '#000';
            var x = start;
            for (var i = 0; i < info.modules.length; i++) {
                var w = info.modules[i] * dpm;
                if (i % 2 === 0) ctx.fillRect(x, top, w, bottom - top);
                x += w;
            }
        });
    }

    function loadHtml2Canvas() {
        if (window.html2canvas) return Promise.resolve(window.html2canvas);
        return new Promise(function(resolve, reject) {
            var s = document.createElement('script');
            s.src = HTML2CANVAS_URL;
            s.onload = function() {
                if (window.html2canvas) resolve(window.html2canvas);
                else reject(new Error('Could not load the label renderer'));
            };
            s.onerror = function() {
                reject(new Error('Could not load the label renderer. Check the internet connection.'));
            };
            document.head.appendChild(s);
        });
    }

    function waitForFrame(frame) {
        var doc = frame.contentDocument;
        var imgs = Array.prototype.slice.call(doc.images || []);
        var imagesReady = Promise.all(imgs.map(function(img) {
            if (img.complete) return Promise.resolve();
            return new Promise(function(resolve) {
                img.onload = img.onerror = function() { resolve(); };
            });
        }));
        var fontsReady = doc.fonts && doc.fonts.ready ? doc.fonts.ready.catch(function() {}) : Promise.resolve();
        return Promise.all([imagesReady, fontsReady]);
    }

    // Renders the same HTML the print dialog uses, one capture per label row
    function printHtml(html, pageWidthMm, pageHeightMm) {
        pageWidthMm = parseFloat(pageWidthMm) || 105;
        pageHeightMm = parseFloat(pageHeightMm) || 25;
        var clean = String(html || '').replace(/<script[\s\S]*?<\/script>/gi, '');
        var hideHelp = '<style>.print-help,.mobile-print-toolbar{display:none!important}body{padding:0!important}</style>';
        clean = /<\/head>/i.test(clean) ? clean.replace(/<\/head>/i, hideHelp + '</head>') : hideHelp + clean;

        return loadHtml2Canvas().then(function(html2canvas) {
            var frame = document.createElement('iframe');
            frame.setAttribute('aria-hidden', 'true');
            frame.style.cssText = 'position:fixed;left:-20000px;top:0;width:' + (pageWidthMm + 10) + 'mm;height:' + (pageHeightMm * 4) + 'mm;border:0;background:#fff;';
            document.body.appendChild(frame);

            function cleanup() {
                try { frame.remove(); } catch (e) {}
            }

            return new Promise(function(resolve) {
                frame.onload = function() { resolve(); };
                frame.srcdoc = clean;
            }).then(function() {
                return waitForFrame(frame);
            }).then(function() {
                var doc = frame.contentDocument;
                var rows = Array.prototype.slice.call(doc.querySelectorAll('table.row, table.page, .tsc-label-page'));
                if (!rows.length) throw new Error('No labels to print');
                var cache = {};
                var canvases = [];
                var keys = [];
                var i = 0;
                function next() {
                    if (i >= rows.length) return Promise.resolve();
                    var el = rows[i++];
                    var key = el.outerHTML;
                    var ready = cache[key] || (cache[key] = collectBarcodes(el).then(function(barcodes) {
                        return html2canvas(el, {
                            scale: 4,
                            backgroundColor: '#ffffff',
                            logging: false,
                            useCORS: true
                        }).then(function(canvas) {
                            canvas.__barcodes = barcodes;
                            return canvas;
                        });
                    }));
                    return ready.then(function(canvas) {
                        canvases.push(canvas);
                        keys.push(key);
                    }).then(next);
                }
                return next().then(function() {
                    return { canvases: canvases, keys: keys };
                });
            }).then(function(result) {
                cleanup();
                return deliver(result.canvases, result.keys, pageWidthMm, pageHeightMm);
            }, function(err) {
                cleanup();
                throw err;
            });
        });
    }

    function init(opts) {
        els.select = document.querySelector(opts.select);
        els.button = opts.button ? document.querySelector(opts.button) : null;
        els.status = opts.status ? document.querySelector(opts.status) : null;
        els.lang = opts.language ? document.querySelector(opts.language) : null;
        els.langWrap = opts.languageWrap ? document.querySelector(opts.languageWrap) : null;
        if (!els.select) return;

        if (els.lang) {
            try { els.lang.value = localStorage.getItem(LANG_KEY) || 'tspl'; } catch (e) {}
            els.lang.addEventListener('change', function() {
                try { localStorage.setItem(LANG_KEY, els.lang.value); } catch (e) {}
            });
        }
        if (els.button) {
            if (!hasWebBluetooth()) {
                els.button.classList.add('d-none');
            }
            els.button.addEventListener('click', chooseDevice);
        }
        els.select.addEventListener('change', function() {
            try { localStorage.setItem(TARGET_KEY, els.select.value); } catch (e) {}
            syncUi();
        });
        fillSelect();
    }

    window.LabelBluetoothPrint = {
        init: init,
        refreshPrinters: refreshPrinters,
        target: target,
        printRows: printRows,
        printHtml: printHtml
    };
})();
</script>

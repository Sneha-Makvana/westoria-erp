# Barcode Scanner - Code Summary

## Added to: `resources/views/purchase/addpurchase.blade.php`

### 1. Barcode Scanner Button (Line ~525)

```html
<div class="purchase-product-row-actions mb-2">
    <button type="button" class="btn btn-sm manage_btn add-product-btn">Add Product</button>
    <button type="button" id="purchaseScanBarcodeBtn" class="btn btn-sm" 
        style="background: #ff9f43; border: 1px solid #ff9f43; color: #fff; margin-left: 8px; padding: 5px 12px; font-size: 12px; border-radius: 4px;">
        <i class="fas fa-barcode"></i> Scan Barcode
    </button>
</div>
```

### 2. Scanner Modal (Line ~823)

```html
<!-- Barcode Scanner Modal for Purchase -->
<div class="modal fade" id="purchaseBarcodeScannerModal" tabindex="-1" aria-labelledby="purchaseBarcodeScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseBarcodeScannerModalLabel">Scan Product Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body">
                <div id="purchase-qr-reader" style="width:100%; min-height:300px;"></div>
                <div id="purchase-scan-message" class="text-center mt-2 small text-muted">
                    Initializing camera...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
```

### 3. JavaScript Scanner Functions (Line ~3233-3540)

#### Library Loader
```javascript
// Load HTML5-QRCode library
if (typeof Html5Qrcode === 'undefined') {
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js';
    document.head.appendChild(script);
}
```

#### Audio Beep
```javascript
function playBeep() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function beep(time, freq) {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.type = "square";
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime + time);
            gain.gain.setValueAtTime(1, audioCtx.currentTime + time);
            osc.start(audioCtx.currentTime + time);
            osc.stop(audioCtx.currentTime + time + 0.2);
        }
        beep(0, 1200);      // First beep
        beep(0.25, 1500);   // Second beep
    } catch (e) {
        console.log('Beep audio not available');
    }
}
```

#### Success Handler
```javascript
async function onPurchaseScanSuccess(decodedText) {
    console.log("Purchase scan success:", decodedText);
    playBeep();
    stopPurchaseScanner();
    document.activeElement && document.activeElement.blur();
    $('#purchaseBarcodeScannerModal').modal('hide');
    $('#purchase-scan-message').text('');

    try {
        const product = await fetchPurchaseProductByBarcode(decodedText);
        if (product) {
            addProductToPurchaseRow(product);
        }
    } catch (error) {
        console.log(error);
    }
}
```

#### Main Initialization
```javascript
function startPurchaseScanner() {
    console.log("startPurchaseScanner called");
    waitForPurchaseLibrary(function() {
        if (purchaseHtml5QrCode) {
            let stopPromise = purchaseHtml5QrCode.isScanning ?
                purchaseHtml5QrCode.stop() :
                Promise.resolve();
            stopPromise.then(() => {
                purchaseHtml5QrCode = null;
                initPurchaseScanner();
            }).catch(() => {
                purchaseHtml5QrCode = null;
                initPurchaseScanner();
            });
        } else {
            initPurchaseScanner();
        }
    });
}

function initPurchaseScanner() {
    try {
        purchaseHtml5QrCode = new Html5Qrcode("purchase-qr-reader");
    } catch (e) {
        console.error("Failed to create Html5Qrcode:", e);
        return;
    }

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 }
    };

    $('#purchase-scan-message').text('Starting camera...');

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            let cameraId = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('rear')
            )?.id;

            if (!cameraId) {
                cameraId = devices[0].id;
            }

            purchaseHtml5QrCode.start(
                cameraId,
                config,
                onPurchaseScanSuccess,
                onPurchaseScanError
            ).then(() => {
                $('#purchase-scan-message').text('Point camera at barcode').css('color', 'green');
            }).catch(err => {
                console.error("Camera start failed:", err);
                showPurchaseManualBarcodeInput();
            });
        } else {
            showPurchaseManualBarcodeInput();
        }
    }).catch(err => {
        console.error("Camera error:", err);
        showPurchaseManualBarcodeInput();
    });
}
```

#### API Call
```javascript
function fetchPurchaseProductByBarcode(barcode) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/api/product-by-barcode/' + encodeURIComponent(barcode),
            type: 'GET',
            headers: {
                "Authorization": "Bearer " + authToken,
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                resolve(response.status && response.product ? response.product : null);
            },
            error: function(xhr) {
                reject(xhr.responseJSON?.message || 'Network error');
            }
        });
    });
}
```

#### Product Row Population
```javascript
function addProductToPurchaseRow(product) {
    // Get the first visible product row
    let $firstRow = $('.purchase-product-row').first();

    if (!$firstRow.length) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No product row found'
        });
        return;
    }

    const productId = String(product.id || '').trim();
    const numericId = parseInt(productId, 10) || 0;
    const categoryId = product.category_id || 0;
    const categorySelect = $firstRow.find('.category-select');
    
    // Add category if not exists
    if (!categorySelect.find(`option[value="${categoryId}"]`).length) {
        categorySelect.append(new Option(product.category_name || 'Unknown', categoryId, false, false));
    }
    categorySelect.val(categoryId).trigger('change');

    // Wait for product dropdown to populate
    setTimeout(() => {
        const productSelect = $firstRow.find('.product-select');
        
        if (!productSelect.find(`option[value="${productId}"]`).length) {
            const gstVal = typeof product.product_gst === 'object' ?
                JSON.stringify(product.product_gst) :
                (product.product_gst || '');
            
            productSelect.append(
                `<option value="${productId}" data-price="${product.price}" data-category="${categoryId}" data-gst-option="${product.gst_option || 'without_gst'}" data-gst='${gstVal}'>${product.name}</option>`
            );
        }

        productSelect.val(productId).trigger('change');

        // Set price and quantity
        const priceInput = $firstRow.find('.price-input');
        const quantityInput = $firstRow.find('.quantity-input');

        priceInput.val(product.price || 0).trigger('input');
        quantityInput.val(1).trigger('input');

        // Show success and scroll
        Swal.fire({
            icon: 'success',
            title: 'Product Added',
            text: product.name + ' added to purchase',
            timer: 1500,
            showConfirmButton: false
        });

        $firstRow[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }, 300);
}
```

#### Event Listeners
```javascript
$(document).ready(function() {
    $('#purchaseScanBarcodeBtn').on('click', function() {
        $('#purchaseBarcodeScannerModal').modal('show');
        setTimeout(() => {
            startPurchaseScanner();
        }, 500);
    });

    $('#purchaseBarcodeScannerModal').on('hidden.bs.modal', function() {
        stopPurchaseScanner();
    });
});
```

## Variable Names Used

| Variable | Type | Purpose |
|----------|------|---------|
| `purchaseHtml5QrCode` | Global | Stores QR code scanner instance |
| `authToken` | Global | Auth token from localStorage |
| `decodedText` | String | Scanned barcode value |
| `$firstRow` | jQuery | First product row in form |
| `categoryId` | Number | Category ID for product |
| `productId` | String | Product ID from barcode |

## ID/Class References

| Element | Type | ID/Class |
|---------|------|----------|
| Scanner Button | ID | `purchaseScanBarcodeBtn` |
| Scanner Modal | ID | `purchaseBarcodeScannerModal` |
| QR Reader | ID | `purchase-qr-reader` |
| Status Message | ID | `purchase-scan-message` |
| Category Select | Class | `.category-select` |
| Product Select | Class | `.product-select` |
| Price Input | Class | `.price-input` |
| Quantity Input | Class | `.quantity-input` |
| Product Row | Class | `.purchase-product-row` |

## Flow Diagram

```
User clicks Scan Button
    ↓
Modal Opens
    ↓
Camera Request (Browser)
    ↓
User Grants Permission
    ↓
Camera Starts
    ↓
Barcode Scanned
    ↓
Beep Sound Plays
    ↓
API Call: /api/product-by-barcode/{barcode}
    ↓
Product Data Received
    ↓
Populate Product Row:
  - Set Category
  - Filter & Set Product
  - Set Price
  - Set Quantity (1)
    ↓
Show Success Message
    ↓
Scroll to Row
    ↓
Modal Closes
    ↓
User Can Continue Adding Products or Submit Form
```

## Dependencies

- **jQuery**: For DOM manipulation
- **SweetAlert2**: For notifications and confirmations
- **HTML5-QRCode**: For barcode scanning (loaded from CDN)
- **Bootstrap**: For modal styling
- **Web Audio API**: For beep sound

## Notes

- The scanner works on first product row only
- If manual entry is needed, falls back gracefully
- GST information is automatically handled
- All prices and totals update automatically
- The modal properly closes after scan
- Camera is properly released when modal closes

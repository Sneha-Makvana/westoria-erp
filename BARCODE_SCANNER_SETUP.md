# Barcode Scanner Implementation for Purchase Form

## Overview
This document describes the barcode/QR code scanner functionality that has been added to the Purchase form (`addpurchase.blade.php`), similar to the existing functionality in the POS (Sales) form.

## Features Implemented

### 1. **Barcode Scanner Button**
- Location: In the purchase product row actions (next to "Add Product" button)
- Button ID: `purchaseScanBarcodeBtn`
- Visual: Orange button with barcode icon
- Triggers: `purchaseBarcodeScannerModal` modal

### 2. **Scanner Modal**
- Modal ID: `purchaseBarcodeScannerModal`
- Contains QR code reader container: `purchase-qr-reader`
- Status messages: `purchase-scan-message`
- Includes manual barcode input fallback for devices without camera

### 3. **Barcode Scanning Functionality**

#### Main Functions:

**`startPurchaseScanner()`**
- Initializes the QR code scanner
- Loads HTML5-QRCode library dynamically
- Requests camera permissions from the user

**`initPurchaseScanner()`**
- Creates Html5Qrcode instance
- Configures camera (prefers rear/back camera)
- Starts barcode scanning
- Includes fallback to manual entry if camera fails

**`onPurchaseScanSuccess(decodedText)`**
- Triggered when barcode is successfully scanned
- Plays beep sound for confirmation
- Fetches product details from API
- Automatically populates the first product row with:
  - Category
  - Product name
  - Price
  - Quantity (set to 1)
  - GST information

**`stopPurchaseScanner()`**
- Stops the camera and barcode scanning
- Cleans up the scanner instance
- Called when modal is closed

**`fetchPurchaseProductByBarcode(barcode)`**
- AJAX call to `/api/product-by-barcode/{barcode}`
- Returns product object with all details
- Requires Bearer token authentication

**`addProductToPurchaseRow(product)`**
- Populates the product row with scanned product data
- Sets category and triggers filtering
- Automatically sets price and quantity
- Shows success notification
- Scrolls to the populated row

#### Manual Barcode Entry:

If the device doesn't have a camera, users can manually enter the barcode:
- Fallback UI appears automatically
- Users type barcode and click "Search" or press Enter
- Same product fetching and population occurs

### 4. **Audio Feedback**

**`playBeep()`**
- Creates a double-beep sound using Web Audio API
- First beep: 1200Hz (low)
- Second beep: 1500Hz (high)
- Provides user confirmation of successful scan

### 5. **Library Loading**

The HTML5-QRCode library is loaded from CDN:
```javascript
https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js
```

Loaded dynamically when the scanner is first initialized.

## How It Works

### Step-by-Step Process:

1. **User clicks "Scan Barcode" button**
   - Modal opens
   - Scanner initialization begins
   - Camera request appears to user

2. **User grants camera permission**
   - Camera feed displays in the modal
   - "Point camera at barcode" message appears
   - Scanner begins reading barcodes

3. **User scans a product barcode**
   - Barcode detected
   - Beep sound plays
   - Product data fetched from API
   - Modal closes
   - Product row populated automatically
   - Success notification shows

4. **Automatic Population**
   - Category select sets the category
   - Product select filters and shows the product
   - Price input populated with product price
   - Quantity set to 1
   - Total amount calculated automatically
   - Page scrolls to the row

## API Endpoint Required

### GET `/api/product-by-barcode/{barcode}`

**Expected Response:**
```json
{
  "status": true,
  "product": {
    "id": 1,
    "name": "Product Name",
    "price": 100.50,
    "category_id": 5,
    "category_name": "Category Name",
    "gst_option": "with_gst",
    "product_gst": [
      {
        "tax_id": 1,
        "tax_name": "SGST",
        "tax_rate": 9
      },
      {
        "tax_id": 2,
        "tax_name": "CGST",
        "tax_rate": 9
      }
    ]
  }
}
```

## Browser Requirements

- **Modern Browsers**: Chrome, Firefox, Edge, Safari (latest versions)
- **Camera Access**: Device must have camera capability
- **HTTPS**: Required for camera access (exception: localhost)
- **Permissions**: User must grant camera access when prompted

## Implementation Details

### Scanner Configuration:
```javascript
{
  fps: 10,                      // 10 frames per second
  qrbox: {
    width: 250,
    height: 250
  }
}
```

### Camera Selection Logic:
1. Attempts to use rear/back camera (preferred)
2. Falls back to front camera if rear unavailable
3. Falls back to any available camera
4. Shows manual input option if no camera found

## Files Modified

- `resources/views/purchase/addpurchase.blade.php`
  - Added barcode scanner modal HTML
  - Added JavaScript functions for scanning
  - Added library loader script

## Testing the Feature

### Manual Test:

1. Navigate to Add Purchase form
2. Click "Scan Barcode" button
3. Allow camera permission
4. Point camera at any barcode
5. Verify:
   - Barcode scans successfully
   - Product details appear in the row
   - Category is set correctly
   - Price is populated
   - Quantity is set to 1
   - Total amount updates

### Test with Manual Entry:

1. Click "Scan Barcode" button on device without camera
2. Manual entry form appears
3. Type a known product barcode
4. Press Enter or click "Search"
5. Verify product populates correctly

## Troubleshooting

### Camera Not Found
- Device may not have camera
- Manual barcode entry will be offered automatically
- Check browser permissions for camera access

### Barcode Not Scanned
- Ensure barcode is well-lit and in focus
- Move camera closer or farther from barcode
- Ensure barcode is not damaged or faded
- Try different angles

### Product Not Found
- Verify barcode exists in the system
- Check that product barcode field is populated in database
- Verify API endpoint is working

### Beep Sound Not Playing
- Check device volume settings
- Browser may have audio restrictions
- Some browsers require user interaction before playing audio

## Security Considerations

- Barcode scanner uses standard browser camera API
- No data is sent to external services (scanning happens locally)
- Product API call requires authentication (Bearer token)
- All data is validated server-side

## Future Enhancements

Possible improvements:
1. Batch scanning (add multiple products quickly)
2. Quantity adjustment during scan
3. Scanner history/recent barcodes
4. Barcode validation before API call
5. Support for different barcode formats (EAN, UPC, etc.)
6. Scan rate limiting to prevent accidental duplicates

## Support

For issues or questions about the barcode scanner:
1. Check browser console for errors
2. Verify camera permissions
3. Test API endpoint manually
4. Check network tab in developer tools
5. Verify barcode format is correct

---

**Date Implemented:** June 2026
**Status:** Active and Functional

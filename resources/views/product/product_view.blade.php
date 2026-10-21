@extends('layout.app')

@section('title', 'Product View')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .product-gallery-wrap {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .product-gallery-carousel {
        touch-action: pan-y;
        user-select: none;
    }

    .product-gallery-carousel .carousel-inner {
        background: #f8f9fa;
    }

    .product-main-img {
        height: 350px;
        object-fit: contain;
        width: 100%;
        background: #fff;
        cursor: zoom-in;
        -webkit-user-drag: none;
    }

    .product-preview-open {
        position: absolute;
        left: 10px;
        top: 10px;
        z-index: 3;
        border: 0;
        background: rgba(27, 40, 80, 0.75);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .pv-lightbox {
        position: fixed;
        inset: 0;
        z-index: 2000;
        background: rgba(10, 14, 28, 0.92);
        display: none;
        flex-direction: column;
        user-select: none;
    }

    .pv-lightbox.show {
        display: flex;
    }

    .pv-lightbox-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .pv-lightbox-btn {
        border: 0;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pv-lightbox-btn:hover {
        background: rgba(255, 159, 67, 0.9);
    }

    .pv-lightbox-stage {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        touch-action: none;
    }

    .pv-lightbox-img {
        max-width: calc(100% - 120px);
        max-height: 100%;
        object-fit: contain;
        cursor: zoom-in;
        transition: transform 0.2s ease;
        -webkit-user-drag: none;
        transform-origin: center center;
        will-change: transform;
    }

    .pv-lightbox-img.zoomed {
        cursor: grab;
        transition: none;
    }

    .pv-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        width: 46px;
        height: 46px;
        font-size: 22px;
    }

    .pv-lightbox-prev { left: 14px; }
    .pv-lightbox-next { right: 14px; }

    .pv-lightbox-thumbs {
        display: flex;
        gap: 8px;
        justify-content: center;
        overflow-x: auto;
        padding: 10px 14px 14px;
    }

    .pv-lightbox-thumbs button {
        flex: 0 0 auto;
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 0;
        background: transparent;
        opacity: 0.6;
    }

    .pv-lightbox-thumbs button.active {
        border-color: #ff9f43;
        opacity: 1;
    }

    .pv-lightbox-thumbs img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 6px;
        display: block;
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .pv-lightbox-img {
            max-width: 100%;
        }

        .pv-lightbox-nav {
            width: 38px;
            height: 38px;
            font-size: 18px;
        }
    }

    .product-gallery-carousel .carousel-control-prev,
    .product-gallery-carousel .carousel-control-next {
        width: 42px;
        opacity: 1;
    }

    .product-gallery-carousel .carousel-control-prev-icon,
    .product-gallery-carousel .carousel-control-next-icon {
        width: 2rem;
        height: 2rem;
        background-color: rgba(27, 40, 80, 0.75);
        border-radius: 50%;
        background-size: 55%;
    }

    .product-gallery-carousel .carousel-indicators {
        margin-bottom: 0.5rem;
    }

    .product-gallery-carousel .carousel-indicators [data-bs-target] {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #adb5bd;
    }

    .product-gallery-carousel .carousel-indicators .active {
        background-color: #ff9f43;
    }

    .product-image-counter {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 3;
        background: rgba(27, 40, 80, 0.75);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
    }

    .product-thumb-row {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding: 10px 12px 12px;
        background: #fff;
        border-top: 1px solid #eee;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }

    .product-thumb-row::-webkit-scrollbar {
        height: 6px;
    }

    .product-thumb-row::-webkit-scrollbar-thumb {
        background: #d0d0d0;
        border-radius: 10px;
    }

    .product-thumb-btn {
        flex: 0 0 auto;
        border: 2px solid transparent;
        border-radius: 8px;
        padding: 0;
        background: transparent;
        scroll-snap-align: start;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .product-thumb-btn.active,
    .product-thumb-btn:hover {
        border-color: #ff9f43;
        transform: translateY(-1px);
    }

    .product-thumb-img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 6px;
        display: block;
    }

    .product-swipe-hint {
        text-align: center;
        font-size: 12px;
        color: #6c757d;
        padding: 0 12px 10px;
        background: #fff;
    }

    @media (max-width: 767.98px) {
        .product-main-img {
            height: 280px;
        }

        .product-gallery-carousel .carousel-control-prev,
        .product-gallery-carousel .carousel-control-next {
            display: none;
        }
    }

    /* Barcode container wrapper for layout */
    .product-barcode .barcode-wrapper {
        width: 100%;
        /* max-width: 300px; */
        /* margin: 0 auto; */
        text-align: center;
    }

    /* Make sure SVG or IMG inside barcode scales well */
    .product-barcode svg,
    .product-barcode img {
        width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /* Optional: improve spacing for barcode text */
    .product-barcode .text-muted {
        margin-top: 0.5rem;
    }

    /* Optional: Responsive tweaks for smaller screens */
    @media (max-width: 576px) {
        .product-barcode {
            width: 100%;
            overflow-x: auto;
            /* Enables horizontal scroll if needed */
            padding: 0 10px;
            /* Adds some padding on small screens */
            text-align: center;
        }

        /* Barcode SVG or image scales responsively */
        .product-barcode svg,
        .product-barcode img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Barcode label (like PRD388788907KK) spacing */
        .product-barcode .text-muted {
            margin-top: 0.5rem;
            word-break: break-word;
            /* Wrap long barcode text */
            font-size: 14px;
        }

    }
</style>

<div class="content">
    <div class="page-header">
        <div class="page-title">
            <h4>Product Details</h4>
            <!-- <h6>Full details of a product</h6> -->
        </div>
        <div class="page-btn d-flex gap-2">
            @if (app('hasPermission')(1, 'edit'))
              <a href="{{ route('product.edit', $view_id) }}" class="btn btn-added">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            @endif
            @if (app('hasPermission')(1, 'view'))
            <a href="{{ route('product.list') }}" class="btn" style="background: #1b2850; color: #fff;">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @endif
        </div>
    </div>


    <div class="card shadow-sm border-0">
        <div class="card-body ">
            <div class="row align-items-start" id="product-details">

                <!-- Product Images -->



            </div>
        </div>
    </div>
</div>

<div class="pv-lightbox" id="productImagePreview" aria-hidden="true">
    <div class="pv-lightbox-top">
        <span id="pvLightboxCounter"></span>
        <span class="d-flex gap-2">
            <button type="button" class="pv-lightbox-btn" id="pvLightboxZoom" title="Zoom"><i class="bi bi-zoom-in"></i></button>
            <button type="button" class="pv-lightbox-btn" id="pvLightboxClose" title="Close"><i class="bi bi-x-lg"></i></button>
        </span>
    </div>
    <div class="pv-lightbox-stage" id="pvLightboxStage">
        <button type="button" class="pv-lightbox-btn pv-lightbox-nav pv-lightbox-prev" id="pvLightboxPrev" title="Previous"><i class="bi bi-chevron-left"></i></button>
        <img src="" alt="Product image preview" class="pv-lightbox-img" id="pvLightboxImg" draggable="false">
        <button type="button" class="pv-lightbox-btn pv-lightbox-nav pv-lightbox-next" id="pvLightboxNext" title="Next"><i class="bi bi-chevron-right"></i></button>
    </div>
    <div class="pv-lightbox-thumbs" id="pvLightboxThumbs"></div>
</div>

<!-- Barcode History Modal -->
<div class="modal fade" id="barcodeHistoryModal" tabindex="-1" aria-labelledby="barcodeHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeHistoryModalLabel"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Barcode History - <span id="bhProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
            </div>
            <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Old Details</th>
                                <th>New Details</th>
                                <th>Changed Date</th>
                            </tr>
                        </thead>
                        <tbody id="barcodeHistoryModalBody">
                            <!-- Loaded dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<script>
    $(document).ready(function() {
        var authToken = localStorage.getItem("authToken");

        let productId = "{{ $view_id }}";

        $.ajax({
            url: "/api/product-view/" + productId,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + authToken,
            },
            success: function(data) {
                // Function to capitalize first letter of each word
                // console.log("API Response:", data); // Debugging log
                function capitalizeWords(str) {
                    if (!str || str.trim() === '') return 'N/A';
                    return str.replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                }
                // Extract product object
                let product = data.product || {};

                // Parse images from product
                let images = JSON.parse(product.images || "[]");
                const hasRealImages = images.length > 0;

                if (images.length === 0) {
                    images = ["/admin/assets/img/product/noimage.png"];
                }

                window.productPreviewImages = hasRealImages ? images.map(function(img) {
                    const imagePath = "{{ env('ImagePath') }}";
                    return img.startsWith('/admin/assets/') || img.startsWith('admin/assets')
                        ? `${imagePath}${img}`
                        : `${imagePath}/storage/${img}`;
                }) : [];

                let imageHTML = `
        <div class="product-gallery-wrap">
            <div id="productCarousel" class="carousel slide product-gallery-carousel" data-bs-ride="false" data-bs-touch="true" data-bs-interval="false">
                ${hasRealImages ? `<button type="button" class="product-preview-open" data-index="0"><i class="bi bi-arrows-fullscreen me-1"></i>View</button>` : ''}
                ${images.length > 1 ? `<div class="product-image-counter" id="productImageCounter">1 / ${images.length}</div>` : ''}
                ${images.length > 1 ? `<div class="carousel-indicators">
                    ${images.map((img, i) => `<button type="button" data-bs-target="#productCarousel" data-bs-slide-to="${i}" ${i === 0 ? 'class="active" aria-current="true"' : ''} aria-label="Image ${i + 1}"></button>`).join('')}
                </div>` : ''}
                <div class="carousel-inner">
    `;

                images.forEach((img, i) => {
                    const imagePath = "{{ env('ImagePath') }}";

                    let imgSrc = img.startsWith('/admin/assets/') || img.startsWith(
                            'admin/assets') ?
                        `${imagePath}${img}` :
                        `${imagePath}/storage/${img}`;
                    imageHTML += `
            <div class="carousel-item ${i === 0 ? 'active' : ''}">
                <img src="${imgSrc}" class="d-block w-100 product-main-img"
                    alt="Product image ${i + 1}" data-index="${i}" draggable="false"
                    ${hasRealImages ? '' : 'style="cursor:default;"'}
                    onerror="this.onerror=null;this.src='/admin/assets/img/product/noimage.png';"
                >
            </div>
        `;
                });

                imageHTML += `
                </div>
                ${images.length > 1 ? `
                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev" aria-label="Previous image">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next" aria-label="Next image">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
                ` : ''}
            </div>
            ${images.length > 1 ? `
            <div class="product-thumb-row" id="productThumbRow">
                ${images.map((img, i) => {
                    const imagePath = "{{ env('ImagePath') }}";
                    let thumbSrc = img.startsWith('/admin/assets/') || img.startsWith('admin/assets')
                        ? `${imagePath}${img}`
                        : `${imagePath}/storage/${img}`;
                    return `<button type="button" class="product-thumb-btn ${i === 0 ? 'active' : ''}" data-index="${i}" aria-label="Show image ${i + 1}">
                        <img src="${thumbSrc}" class="product-thumb-img" alt="Thumbnail ${i + 1}" onerror="this.onerror=null;this.src='/admin/assets/img/product/noimage.png';">
                    </button>`;
                }).join('')}
            </div>
            <div class="product-swipe-hint"><i class="bi bi-hand-index-thumb me-1"></i>Swipe or tap thumbnails to view images</div>
            ` : ''}
        </div>
    `;

                // Get currency info from root data
                let currencySymbol = data.currencySymbol || '₹';
                let currencyPosition = data.currencyPosition || 'left';

                // Format GST details
                let gstDetailsHTML = 'N/A';
                if (product.gst_option === 'with_gst' && product.product_gst) {
                    try {
                        let gstData = JSON.parse(product.product_gst);
                        gstDetailsHTML = gstData.map(gst => `${gst.tax_name} (${gst.tax_rate}%)`).join(', ');
                    } catch (e) {
                        console.error("Error parsing product_gst:", e);
                    }
                }

                // Format price dynamically
                let price = product.price ?? 'N/A';
                let formattedPrice = 'N/A';

                if (price !== 'N/A') {
                    let priceVal = parseFloat(price).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    formattedPrice = currencyPosition === 'left' ?
                        `${currencySymbol}${priceVal}` :
                        `${priceVal}${currencySymbol}`;
                }

                let imeiHTML = '';
                if (product.imei_no && product.imei_no !== '[]') {
                    try {
                        let imeis = JSON.parse(product.imei_no);
                        if (Array.isArray(imeis) && imeis.length > 0) {
                            imeiHTML = `
                                <div class="col-12">
                                    <p class="mb-1"><i class="bi bi-fingerprint me-2"></i><strong>IMEI/Serial Numbers:</strong> ${imeis.join(', ')}</p>
                                    <hr class="my-1">
                                </div>
                            `;
                        }
                    } catch (e) {
                        imeiHTML = `
                            <div class="col-12">
                                <p class="mb-1"><i class="bi bi-fingerprint me-2"></i><strong>IMEI/Serial Numbers:</strong> ${product.imei_no}</p>
                                <hr class="my-1">
                            </div>
                        `;
                    }
                }

                let rentDetailsHTML = '';
                @if(\App\Models\Setting::first()?->rental)
                rentDetailsHTML = (product.is_rent_available == 1 || product.is_rent_available === true || product.is_rent_available === '1')
                    ? `<div class="col-6">
                        <p class="mb-1"><i class="bi bi-calendar-check me-2"></i><strong>Rent Availability:</strong> <span class="badge bg-success">Yes</span></p>
                        <hr class="my-1">
                       </div>
                       <div class="col-6">
                        <p class="mb-1"><i class="bi bi-clock me-2"></i><strong>Rent Type:</strong> ${capitalizeWords(product.rent_period ?? 'N/A')}</p>
                        <hr class="my-1">
                       </div>
                       <div class="col-6">
                        <p class="mb-1"><i class="bi bi-currency-rupee me-2"></i><strong>Rent Price:</strong> ${product.rent_price ?? 'N/A'}</p>
                        <hr class="my-1">
                       </div>`
                    : `<div class="col-6">
                        <p class="mb-1"><i class="bi bi-calendar-x me-2"></i><strong>Rent Availability:</strong> <span class="badge bg-secondary">No</span></p>
                        <hr class="my-1">
                       </div>`;
                @endif

                let variantsHTML = '';
                if (product.variants && product.variants.length > 0) {
                    let rows = product.variants.map((v, i) => {
                        return `
                            <tr>
                                <td>${v.size || '-'}</td>
                                <td>${v.color || '-'}</td>
                                <td>${v.qty || '0'}</td>
                                <td>${v.purchase_price ? parseFloat(v.purchase_price).toFixed(2) : '0.00'}</td>
                                <td>${v.price ? parseFloat(v.price).toFixed(2) : '0.00'}</td>
                                <td>${v.barcode || '-'}</td>
                            </tr>
                        `;
                    }).join('');

                    variantsHTML = `
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3"><i class="bi bi-layers me-2"></i>Product Variants</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Quantity</th>
                                                <th>Purchase Price</th>
                                                <th>Sale Price</th>
                                                <th>Barcode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rows}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                }

                let html = `
            <div class="row align-items-start mt-3">
                <div class="col-md-5">
                    <div class="bg-white p-3 rounded border">
                        ${imageHTML}
                    </div>
                </div>
                <div class="col-md-6 mx-3">
                    <h2 class="fw-bold mb-4"><i class="bi bi-box-seam me-2"></i>${capitalizeWords(product.name)}</h2>
                <p class="text-muted mb-2"><i class="bi bi-tags me-1"></i>Category: <strong>${capitalizeWords(product.category?.name)}</strong></p>
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>${formatAvailability(product.availablility ?? 'N/A')}</span>
                    <span class="badge bg-primary ms-2"><i class="bi bi-lightning-fill me-1"></i>${capitalizeWords(product.status)}</span>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-upc-scan me-2"></i><strong>SKU:</strong> ${product.SKU ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-hash me-2"></i><strong>HSN Code:</strong> ${product.hsn_code ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-building me-2"></i><strong>Brand:</strong> ${capitalizeWords(product.brand?.name)}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-percent me-2"></i><strong>GST Option:</strong> ${formatAvailability(product.gst_option ?? 'N/A')}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-receipt me-2"></i><strong>Product GST:</strong> ${gstDetailsHTML}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-arrow-repeat me-2"></i><strong>Stock Status:</strong> ${formatAvailability(product.availablility ?? 'N/A')}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-check-circle me-2"></i><strong>Status:</strong> ${capitalizeWords(product.status ?? 'N/A')}</p>
                        <hr class="my-1">
                    </div>
                     <div class="col-6">
                        <p class="mb-1"><i class="bi bi-grid-fill me-2"></i><strong>Unit:</strong> ${capitalizeWords(product.unit?.unit_name ?? 'N/A')}</p>
                        <hr class="my-1">
                    </div>
                    ${rentDetailsHTML}
                    ${imeiHTML}
                </div>
                
                ${product.item_type !== 'variant' ? `
                <div class="row g-3 mt-2">
                    <div class="col-12"><h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Product Info</h6></div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-currency-rupee me-2"></i><strong>Purchase Price:</strong> ${product.purchase_price ? parseFloat(product.purchase_price).toFixed(2) : '0.00'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-tag-fill me-2"></i><strong>Sale Price:</strong> ${formattedPrice}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-box me-2"></i><strong>Quantity:</strong> ${product.quantity ?? '0'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-tags-fill me-2"></i><strong>MRP:</strong> ${product.mrp ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-upc me-2"></i><strong>Batch No:</strong> ${product.batch_no ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-hash me-2"></i><strong>Label Code:</strong> ${product.label_code ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-card-text me-2"></i><strong>Wholesale:</strong> ${product.label_reference ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-percent me-2"></i><strong>Tax Rate (%):</strong> ${product.tax_rate ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><strong>Item Location:</strong> ${product.item_location ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-battery-half me-2"></i><strong>Min Stock:</strong> ${product.minimum_stock_qty ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-tags me-2"></i><strong>Discount Type:</strong> ${product.discount_type ? capitalizeWords(product.discount_type) : 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-tag me-2"></i><strong>Sale Discount:</strong> ${product.sale_discount ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-check-all me-2"></i><strong>Inclusive of Tax:</strong> ${product.inclusive_of_tax ? 'Yes' : 'No'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-ruler me-2"></i><strong>Secondary Unit:</strong> ${product.secondary_unit?.unit_name ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <div class="col-6">
                        <p class="mb-1"><i class="bi bi-arrow-left-right me-2"></i><strong>Conversion Rate:</strong> ${product.conversion_rate ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div>
                    <!-- <div class="col-6">
                        <p class="mb-1"><i class="bi bi-upc me-2"></i><strong>Barcode Number:</strong> ${product.barcode ?? 'N/A'}</p>
                        <hr class="my-1">
                    </div> -->
                    ${data.barcode_html ? `
                    <div class="col-12 mt-3">
                        <p class="mb-1"><i class="bi bi-upc-scan me-2"></i><strong>Barcode:</strong> ${
                            (function() {
                                if (!product.barcode) return 'N/A';
                                return product.barcode.split(',').map(b => b.trim()).filter(b => b).join(', ');
                            })()
                        }</p>
                        <div class="product-barcode mt-2">
                            <div class="barcode-wrapper d-flex flex-wrap gap-4" data-id="${product.id}">
                                ${data.barcode_html}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
                ` : ''}
                   
                    ${variantsHTML}
                    </div>
                </div>
            </div>
        `;

                $('#product-details').html(html);

                initProductImageCarousel(images.length);
            },

            error: function() {
                $('#product-details').html('<p class="text-danger">Product not found.</p>');
            }
        });
    });

    $(document).on('click', '.single-barcode-history', function(e) {
        e.stopPropagation();
        let clickedBarcode = $(this).attr('data-barcode');
        let productId = $(this).closest('.barcode-wrapper').attr('data-id');
        let authToken = localStorage.getItem("authToken");
        
        $('#bhProductName').text(clickedBarcode);
        $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        $('#barcodeHistoryModal').modal('show');
        
        $.ajax({
            url: `/api/product-barcode-history/${productId}`,
            type: "GET",
            dataType: "json",
            headers: {
                "Authorization": "Bearer " + authToken,
            },
            success: function(response) {
                if (response.status) {
                    let histories = response.data;
                    let html = '';
                    if (histories.length > 0) {
                        $.each(histories, function(index, history) {
                            let date = new Date(history.created_at).toLocaleString();
                            
                            function formatBarcodeDetailsBlock(barcodeStr, detailsStr) {
                                if (!barcodeStr) return '-';
                                let barcodes = barcodeStr.split(',').map(b => b.trim()).filter(b => b);
                                // Filter to only the clicked barcode
                                barcodes = barcodes.filter(b => b === clickedBarcode);
                                if (barcodes.length === 0) return '-';

                                let details = {};
                                if (detailsStr) {
                                    try {
                                        details = typeof detailsStr === 'string' ? JSON.parse(detailsStr) : detailsStr;
                                    } catch(e) {}
                                }
                                
                                if (Object.keys(details).length === 0) {
                                    return barcodes.join(', ');
                                }

                                let result = '<div class="mt-1">';
                                barcodes.forEach(b => {
                                    let d = details[b] || {};
                                    let parts = [];
                                    if (d.price) parts.push(`Price: ${d.price}`);
                                    if (d.mrp) parts.push(`MRP: ${d.mrp}`);
                                    if (d.batch_no) parts.push(`Batch: ${d.batch_no}`);
                                    if (d.label_code) parts.push(`Lbl Code: ${d.label_code}`);
                                    if (d.label_reference) parts.push(`Wholesale: ${d.label_reference}`);
                                    if (d.quantity !== undefined && d.quantity !== '') parts.push(`Qty: ${d.quantity}`);
                                    if (d.specific_qty !== undefined && d.specific_qty !== '') parts.push(`Other: ${d.specific_qty}`);
                                    
                                    if (parts.length > 0) {
                                        result += `<div class="mb-2"><span class="badge bg-secondary">${b}</span><div class="text-wrap text-break mt-1" style="font-size: 0.8em; color: #555; line-height: 1.4;">${parts.join(' | ')}</div></div>`;
                                    } else {
                                        result += `<div class="mb-2"><span class="badge bg-secondary">${b}</span></div>`;
                                    }
                                });
                                result += '</div>';
                                return result;
                            }

                            let oldDisplayBarcode = formatBarcodeDetailsBlock(history.old_barcode, history.old_barcode_details);
                            let newDisplayBarcode = formatBarcodeDetailsBlock(history.new_barcode, history.new_barcode_details);
                            
                            let oldDetails = '';
                            if(history.old_barcode !== null && oldDisplayBarcode !== '-') {
                                oldDetails += `<b>Barcodes:</b> ${oldDisplayBarcode}<br>`;
                            }
                            if(history.old_price !== null) oldDetails += `<b>Main Price:</b> ${history.old_price || '-'}<br>`;
                            if(history.old_mrp !== null) oldDetails += `<b>Main MRP:</b> ${history.old_mrp || '-'}<br>`;
                            if(history.old_batch_no !== null) oldDetails += `<b>Main Batch:</b> ${history.old_batch_no || '-'}<br>`;
                            if(history.old_label_code !== null) oldDetails += `<b>Main Lbl Code:</b> ${history.old_label_code || '-'}<br>`;
                            if(history.old_label_reference !== null) oldDetails += `<b>Main Wholesale:</b> ${history.old_label_reference || '-'}<br>`;
                            if(history.old_qty !== null && history.old_qty !== undefined) oldDetails += `<b>Main Qty:</b> ${history.old_qty}<br>`;
                            if(history.old_unit !== null && history.old_unit !== undefined) oldDetails += `<b>Unit:</b> ${history.old_unit || '-'}<br>`;
                            if(!oldDetails) oldDetails = '-';

                            let newDetails = '';
                            if(history.new_barcode !== null && newDisplayBarcode !== '-') {
                                newDetails += `<b>Barcodes:</b> ${newDisplayBarcode}<br>`;
                            }
                            if(history.new_price !== null) newDetails += `<b>Main Price:</b> ${history.new_price || '-'}<br>`;
                            if(history.new_mrp !== null) newDetails += `<b>Main MRP:</b> ${history.new_mrp || '-'}<br>`;
                            if(history.new_batch_no !== null) newDetails += `<b>Main Batch:</b> ${history.new_batch_no || '-'}<br>`;
                            if(history.new_label_code !== null) newDetails += `<b>Main Lbl Code:</b> ${history.new_label_code || '-'}<br>`;
                            if(history.new_label_reference !== null) newDetails += `<b>Main Wholesale:</b> ${history.new_label_reference || '-'}<br>`;
                            if(history.new_qty !== null && history.new_qty !== undefined) newDetails += `<b>Main Qty:</b> ${history.new_qty}<br>`;
                            if(history.new_unit !== null && history.new_unit !== undefined) newDetails += `<b>Unit:</b> ${history.new_unit || '-'}<br>`;
                            if(!newDetails) newDetails = '-';

                            html += `
                                <tr>
                                    <td>${oldDetails}</td>
                                    <td>${newDetails}</td>
                                    <td class="text-nowrap">${date}</td>
                                </tr>
                            `;
                        });
                        $('#barcodeHistoryModalBody').html(html);
                    } else {
                        $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center text-muted">No barcode history found for this product.</td></tr>');
                    }
                } else {
                    $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load history.</td></tr>');
                }
            },
            error: function() {
                $('#barcodeHistoryModalBody').html('<tr><td colspan="3" class="text-center text-danger">Error loading history.</td></tr>');
            }
        });
    });

    function formatAvailability(str) {
        if (!str) return "";
        return str
            .split('_') // Split by underscore
            .map(word => word.charAt(0).toUpperCase() + word.slice(1)) // Capitalize each word
            .join(' '); // Join with space
    }

    (function() {
        const $box = $('#productImagePreview');
        const img = document.getElementById('pvLightboxImg');
        const MIN_SCALE = 1;
        const MAX_SCALE = 4;
        const TAP_ZOOM = 2;
        let current = 0;
        let scale = 1;
        let panX = 0, panY = 0;
        let dragStart = null;
        let touchStartX = null;
        let downPoint = null;
        let pinch = null;
        let suppressClickZoom = false;

        function list() {
            return Array.isArray(window.productPreviewImages) ? window.productPreviewImages : [];
        }

        function isZoomed() {
            return scale > 1.05;
        }

        function applyTransform() {
            if (scale <= MIN_SCALE) {
                img.style.transform = '';
            } else {
                img.style.transform = `translate(${panX}px, ${panY}px) scale(${scale})`;
            }
            img.classList.toggle('zoomed', isZoomed());
            $('#pvLightboxZoom i').attr('class', isZoomed() ? 'bi bi-zoom-out' : 'bi bi-zoom-in');
        }

        function resetZoom() {
            scale = MIN_SCALE;
            panX = 0;
            panY = 0;
            pinch = null;
            dragStart = null;
            applyTransform();
        }

        function setScale(next, keepPan) {
            scale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, next));
            if (scale <= MIN_SCALE) {
                panX = 0;
                panY = 0;
            } else if (!keepPan) {
                panX = 0;
                panY = 0;
            }
            applyTransform();
        }

        function touchDistance(a, b) {
            const dx = a.clientX - b.clientX;
            const dy = a.clientY - b.clientY;
            return Math.sqrt(dx * dx + dy * dy) || 1;
        }

        function show(index) {
            const images = list();
            if (!images.length) return;
            current = (index + images.length) % images.length;
            resetZoom();
            img.onerror = function() {
                this.onerror = null;
                this.src = '/admin/assets/img/product/noimage.png';
            };
            img.src = images[current];
            const multi = images.length > 1;
            $('#pvLightboxCounter').text(multi ? (current + 1) + ' / ' + images.length : '');
            $('#pvLightboxPrev, #pvLightboxNext').toggle(multi);
            $('#pvLightboxThumbs button').removeClass('active')
                .filter('[data-index="' + current + '"]').addClass('active');
            const activeThumb = $('#pvLightboxThumbs button.active')[0];
            if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center' });
        }

        function open(index) {
            const images = list();
            if (!images.length) return;
            $('#pvLightboxThumbs').html(images.length > 1 ? images.map(function(src, i) {
                return `<button type="button" data-index="${i}"><img src="${src}" alt="Thumbnail ${i + 1}" onerror="this.onerror=null;this.src='/admin/assets/img/product/noimage.png';"></button>`;
            }).join('') : '');
            $box.addClass('show').attr('aria-hidden', 'false');
            $('body').css('overflow', 'hidden');
            show(index);
        }

        function close() {
            $box.removeClass('show').attr('aria-hidden', 'true');
            $('body').css('overflow', '');
            resetZoom();
            const carouselEl = document.getElementById('productCarousel');
            if (carouselEl && list().length > 1 && window.bootstrap && bootstrap.Carousel) {
                bootstrap.Carousel.getOrCreateInstance(carouselEl).to(current);
            }
        }

        function activeCarouselIndex() {
            const idx = $('#productCarousel .carousel-item.active img').attr('data-index');
            return parseInt(idx, 10) || 0;
        }

        $(document).on('pointerdown', '.product-main-img', function(e) {
            downPoint = { x: e.clientX, y: e.clientY };
        });
        $(document).on('click', '.product-main-img', function(e) {
            if (downPoint && (Math.abs(e.clientX - downPoint.x) > 10 || Math.abs(e.clientY - downPoint.y) > 10)) return;
            open(parseInt($(this).attr('data-index'), 10) || 0);
        });
        $(document).on('click', '.product-preview-open', function(e) {
            e.preventDefault();
            e.stopPropagation();
            open(activeCarouselIndex());
        });

        $('#pvLightboxClose').on('click', close);
        $('#pvLightboxPrev').on('click', function(e) { e.stopPropagation(); show(current - 1); });
        $('#pvLightboxNext').on('click', function(e) { e.stopPropagation(); show(current + 1); });
        $('#pvLightboxZoom').on('click', function() {
            if (isZoomed()) setScale(MIN_SCALE);
            else setScale(TAP_ZOOM);
        });
        $('#pvLightboxThumbs').on('click', 'button', function() {
            show(parseInt($(this).attr('data-index'), 10) || 0);
        });
        $('#pvLightboxStage').on('click', function(e) {
            if (e.target === this) close();
        });

        img.addEventListener('dblclick', function(e) {
            e.preventDefault();
            if (isZoomed()) setScale(MIN_SCALE);
            else setScale(TAP_ZOOM);
        });
        img.addEventListener('click', function(e) {
            if (suppressClickZoom || e.detail !== 1) return;
            // Desktop click-to-zoom; skip after pinch / while already zoomed.
            if (isZoomed()) return;
            setTimeout(function() {
                if (!suppressClickZoom && !isZoomed()) setScale(TAP_ZOOM);
            }, 220);
        });

        img.addEventListener('pointerdown', function(e) {
            if (e.pointerType === 'touch') return;
            if (!isZoomed()) return;
            dragStart = { x: e.clientX - panX, y: e.clientY - panY };
            img.setPointerCapture(e.pointerId);
        });
        img.addEventListener('pointermove', function(e) {
            if (e.pointerType === 'touch') return;
            if (!isZoomed() || !dragStart) return;
            panX = e.clientX - dragStart.x;
            panY = e.clientY - dragStart.y;
            applyTransform();
        });
        img.addEventListener('pointerup', function() { dragStart = null; });
        img.addEventListener('pointercancel', function() { dragStart = null; });

        const stage = document.getElementById('pvLightboxStage');

        stage.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                suppressClickZoom = true;
                touchStartX = null;
                dragStart = null;
                pinch = {
                    startDist: touchDistance(e.touches[0], e.touches[1]),
                    startScale: scale,
                    startPanX: panX,
                    startPanY: panY
                };
                return;
            }
            if (e.touches.length === 1) {
                if (isZoomed()) {
                    dragStart = {
                        x: e.touches[0].clientX - panX,
                        y: e.touches[0].clientY - panY
                    };
                    touchStartX = null;
                } else {
                    touchStartX = e.touches[0].clientX;
                    dragStart = null;
                }
            }
        }, { passive: true });

        stage.addEventListener('touchmove', function(e) {
            if (e.touches.length === 2 && pinch) {
                e.preventDefault();
                const dist = touchDistance(e.touches[0], e.touches[1]);
                setScale(pinch.startScale * (dist / pinch.startDist), true);
                return;
            }
            if (e.touches.length === 1 && isZoomed() && dragStart) {
                e.preventDefault();
                panX = e.touches[0].clientX - dragStart.x;
                panY = e.touches[0].clientY - dragStart.y;
                applyTransform();
            }
        }, { passive: false });

        stage.addEventListener('touchend', function(e) {
            if (pinch && e.touches.length < 2) {
                pinch = null;
                if (scale < 1.08) setScale(MIN_SCALE);
                setTimeout(function() { suppressClickZoom = false; }, 300);
            }
            if (e.touches.length === 0) {
                dragStart = null;
            }
            if (touchStartX === null || list().length < 2 || isZoomed()) {
                if (e.touches.length === 0) touchStartX = null;
                return;
            }
            const dx = e.changedTouches[0].clientX - touchStartX;
            touchStartX = null;
            if (Math.abs(dx) > 50) show(dx < 0 ? current + 1 : current - 1);
        });

        stage.addEventListener('touchcancel', function() {
            pinch = null;
            dragStart = null;
            touchStartX = null;
            suppressClickZoom = false;
        });

        // Mouse wheel zoom (desktop)
        stage.addEventListener('wheel', function(e) {
            if (!$box.hasClass('show')) return;
            e.preventDefault();
            const delta = e.deltaY < 0 ? 0.15 : -0.15;
            setScale(scale + delta, true);
        }, { passive: false });

        $(document).on('keydown', function(e) {
            if (!$box.hasClass('show')) return;
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowLeft') show(current - 1);
            else if (e.key === 'ArrowRight') show(current + 1);
        });
    })();

    function initProductImageCarousel(totalImages) {
        const carouselEl = document.getElementById('productCarousel');
        if (!carouselEl || totalImages <= 1) {
            return;
        }

        let carouselInstance = null;
        if (window.bootstrap && bootstrap.Carousel) {
            carouselInstance = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
                interval: false,
                touch: true,
                wrap: true,
                ride: false
            });
        }

        function syncCarouselUi(activeIndex) {
            $('#productImageCounter').text((activeIndex + 1) + ' / ' + totalImages);
            $('.product-thumb-btn').removeClass('active');
            $('.product-thumb-btn[data-index="' + activeIndex + '"]').addClass('active');

            const activeThumb = document.querySelector('.product-thumb-btn[data-index="' + activeIndex + '"]');
            const thumbRow = document.getElementById('productThumbRow');
            if (activeThumb && thumbRow) {
                const targetLeft = activeThumb.offsetLeft - (thumbRow.clientWidth / 2) + (activeThumb.clientWidth / 2);
                thumbRow.scrollTo({ left: Math.max(0, targetLeft), behavior: 'smooth' });
            }
        }

        carouselEl.addEventListener('slid.bs.carousel', function(event) {
            syncCarouselUi(event.to);
        });

        $(document).off('click.productThumb', '.product-thumb-btn').on('click.productThumb', '.product-thumb-btn', function() {
            const index = parseInt($(this).attr('data-index'), 10);
            if (Number.isNaN(index)) {
                return;
            }
            if (carouselInstance) {
                carouselInstance.to(index);
            } else {
                syncCarouselUi(index);
            }
        });
    }
</script>
@endpush
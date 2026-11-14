@extends('layout.app')

@section('title', 'AI Bill Upload')

@section('content')
    <style>
        .ai-bill-page {
            width: 100%;
            max-width: none;
            font-size: 16px;
        }
        .ai-bill-page .ai-bill-hero h4 { font-size: 25px; }
        .ai-bill-page .ai-bill-hero p { font-size: 16px; max-width: 820px; line-height: 1.55; }
        .ai-bill-page .ai-section-title { font-size: 15px; }
        .ai-bill-page .ai-empty-note { font-size: 15px; }
        .ai-bill-page .form-label { font-size: 15px; font-weight: 600; }
        .ai-bill-page .form-control,
        .ai-bill-page .btn { font-size: 15px; }
        .ai-bill-page .form-control { min-height: 42px; }
        .ai-bill-page .ai-card .card-body { padding: 24px; }
        .ai-bill-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }
        .ai-bill-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff4e8;
            color: #c56a12;
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
        }
        .ai-bill-hero h4 { margin: 8px 0 4px; }
        .ai-bill-hero p { margin: 0; color: #6b7280; max-width: 640px; }
        .ai-dropzone {
            border: 2px dashed #ffc48a;
            background: linear-gradient(180deg, #fffaf4 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: .2s ease;
        }
        .ai-dropzone.is-dragover,
        .ai-dropzone:hover { border-color: #ff9f43; background: #fff7ee; }
        .ai-dropzone input { display: none; }
        .ai-drop-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            border-radius: 16px;
            background: #ff9f43;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .ai-preview {
            display: none;
            margin-top: 16px;
            border-radius: 12px;
            overflow: hidden;
            max-height: 620px;
            border: 1px solid #eee;
        }
        .ai-preview img { width: 100%; height: 560px; object-fit: contain; background: #111827; }
        .ai-review { display: none; }
        .ai-card {
            border: 1px solid #f0e6d8;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(255, 159, 67, .06);
        }
        .ai-card .card-body { padding: 18px; }
        .ai-section-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #8a6a4a;
            margin-bottom: 14px;
        }
        .ai-items-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .ai-items-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .ai-item-search {
            flex: 1 1 260px;
            min-width: 220px;
            position: relative;
        }
        .ai-item-search input {
            width: 100%;
            min-height: 42px;
            padding-right: 110px;
        }
        .ai-item-search-count {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
            pointer-events: none;
        }
        .ai-item-search-empty {
            display: none;
            margin: 0 0 12px;
            padding: 12px 14px;
            border: 1px dashed #eadfce;
            border-radius: 10px;
            color: #6b7280;
            background: #fffaf4;
        }
        .ai-items-summary span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 14px;
            font-weight: 700;
        }
        .ai-count-total { background: #fff3e6; color: #9a4d00; }
        .ai-count-existing { background: #e8f8ef; color: #18794e; }
        .ai-count-new { background: #fff3d6; color: #9a6700; }
        .ai-item-row {
            border: 1px solid #eadfce;
            border-left: 6px solid #18794e;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            background: #fff;
        }
        .ai-item-row.is-new {
            border-left-color: #e0a100;
            background: #fffdf6;
        }
        .ai-item-index {
            font-size: 16px;
            font-weight: 800;
            color: #3d2b1f;
        }
        .ai-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 13px;
            font-weight: 800;
        }
        .ai-badge-update { background: #e8f8ef; color: #18794e; }
        .ai-badge-create { background: #fff3d6; color: #9a6700; }
        .ai-totals {
            width: min(100%, 375px);
            margin-left: auto;
            padding: 10px 14px;
            border: 1px solid #f3e7d8;
            border-radius: 10px;
            background: #fffaf4;
            font-weight: 600;
        }
        .ai-summary-row { display: flex; align-items: flex-start; gap: 16px; }
        .ai-balance-note {
            display: none;
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #f1b7b7;
            border-radius: 10px;
            background: #fff5f5;
            color: #8b1e1e;
            font-size: 17px;
            line-height: 1.55;
        }
        .ai-balance-note strong { display: block; margin-bottom: 6px; font-size: 18px; }
        .ai-totals .ai-total-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding: 5px 0;
            color: #495057;
        }
        .ai-totals .ai-total-row span { color: #263238; text-align: right; }
        .ai-totals .ai-total-payable {
            margin: 5px -14px -10px;
            padding: 10px 14px;
            border-radius: 0 0 10px 10px;
            background: #ff9f43;
            color: #fff;
            font-size: 16px;
        }
        .ai-totals .ai-total-payable span { color: #fff; }
        .ai-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; margin-top: 16px; }
        .btn-ai-primary {
            background: #ff9f43;
            border-color: #ff9f43;
            color: #fff;
            font-weight: 600;
        }
        .btn-ai-primary:hover { background: #f08f2e; color: #fff; }
        .ai-empty-note { color: #6b7280; font-size: 13px; }
        @media (max-width: 767px) {
            .ai-bill-hero { flex-direction: column; }
            .ai-summary-row { flex-direction: column; }
            .ai-totals { width: 100%; }
        }
    </style>

    <div class="content ai-bill-page">
        <div class="ai-bill-hero">
            <div>
                <div class="ai-bill-kicker"><i class="fas fa-magic"></i> Purchase AI</div>
                <h4>Upload bill & review stock</h4>
                <p>Photograph a purchase bill. We fill vendor, bill number, date and every line item. Recheck the fields, then save — existing products get stock, new ones are created. (Retail stock — no batch or expiry.)</p>
            </div>
            <a href="{{ route('purchase.lists') }}" class="btn btn-cancel">
                <i class="fas fa-chevron-left"></i> Back
            </a>
        </div>

        @if (! $aiReady)
            <div class="alert alert-warning">
                Add <code>GEMINI_API_KEY</code> or <code>OPENAI_API_KEY</code> to <code>.env</code>, then run
                <code>php artisan config:clear</code>. Without a vision key the bill image cannot be read.
            </div>
        @endif

        <div class="card ai-card mb-3" id="aiUploadCard">
            <div class="card-body">
                <div class="ai-section-title">1. Bill image</div>
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="ai-dropzone d-block h-100" id="aiDropzone" for="aiBillFile">
                            <input type="file" id="aiBillFile" accept="image/jpeg,image/png,image/webp" multiple>
                            <div class="ai-drop-icon"><i class="fas fa-folder-open"></i></div>
                            <h5 class="mb-1">Upload Bill</h5>
                            <p class="mb-0 ai-empty-note">Select bill photo from device</p>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="ai-dropzone d-block h-100" id="aiCameraZone" for="aiCameraFile">
                            <input type="file" id="aiCameraFile" accept="image/jpeg,image/png,image/webp" capture="environment">
                            <div class="ai-drop-icon"><i class="fas fa-camera"></i></div>
                            <h5 class="mb-1">Take Photo</h5>
                            <p class="mb-0 ai-empty-note">Capture bill using camera</p>
                        </label>
                    </div>
                </div>
                <div class="text-center mb-3">
                    <p class="mb-0 ai-empty-note">JPG, PNG or WEBP · up to 10 MB · for a bill with more than one page, select every page of that invoice together</p>
                    <div id="aiFileName" class="mt-2 fw-semibold text-dark"></div>
                </div>
                <div class="ai-preview" id="aiPreview"><img alt="Bill preview"></div>
                <div class="text-center mt-2" id="aiFullPreviewAction" style="display:none;">
                    <a class="btn btn-sm btn-outline-secondary" id="aiOpenFullPreview" target="_blank"><i class="fas fa-expand me-1"></i> Open full-size bill image</a>
                    <span id="aiPreviewCountText" class="ms-2 text-muted" style="display:none;"></span>
                </div>
                <div class="ai-actions">
                    <button type="button" class="btn btn-ai-primary" id="aiExtractBtn" {{ $aiReady ? '' : 'disabled' }}>
                        <i class="fas fa-brain me-1"></i> Read bill
                    </button>
                </div>
            </div>
        </div>

        <div class="ai-review" id="aiReview">
            <div class="card ai-card mb-3">
                <div class="card-body">
                    <div class="ai-section-title">2. Bill details — edit if needed</div>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label">Vendor</label>
                            <select id="aiVendor" class="form-control">
                                <option value="">Create from bill name</option>
                            </select>
                            <input type="text" id="aiVendorName" class="form-control mt-2" placeholder="New vendor name">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label">Phone</label>
                            <input type="text" id="aiVendorPhone" class="form-control" placeholder="Vendor phone">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" id="aiVendorEmail" class="form-control" placeholder="Vendor email">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">GSTIN</label>
                            <input type="text" id="aiVendorGstin" class="form-control" placeholder="Vendor GSTIN">
                        </div>
                        <div class="col-6 col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" id="aiVendorAddress" class="form-control" placeholder="Vendor Address">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Bill no.</label>
                            <input type="text" id="aiBillNo" class="form-control">
                            <div class="text-danger small mt-1" id="aiBillNoError"></div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Purchase date</label>
                            <input type="date" id="aiBillDate" class="form-control">
                        </div>
                        <div class="col-6 col-md-8">
                            <label class="form-label">Remark</label>
                            <input type="text" id="aiRemark" class="form-control" placeholder="Optional note from bill">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label">Status</label>
                            <select id="aiStatus" class="form-control">
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card ai-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="ai-section-title mb-0">3. Materials & stock</div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="aiAddRow">+ Add line</button>
                    </div>
                    <p class="ai-empty-note mb-3">Each card is one product. Green means it is already in stock. Yellow means a new product will be created.</p>
                    <div class="ai-items-toolbar">
                        <div class="ai-items-summary" id="aiItemsSummary">
                            <span class="ai-count-total">Total items: <b id="aiItemsTotalCount">0</b></span>
                            <span class="ai-count-existing">Existing: <b id="aiItemsExistingCount">0</b></span>
                            <span class="ai-count-new">New: <b id="aiItemsNewCount">0</b></span>
                        </div>
                        <label class="ai-item-search">
                            <input type="search" id="aiItemSearch" class="form-control" placeholder="Search product, barcode or HSN" autocomplete="off">
                            <span class="ai-item-search-count" id="aiItemSearchCount"></span>
                        </label>
                    </div>
                    <p class="ai-item-search-empty" id="aiItemSearchEmpty">No product matches that search.</p>
                    <div id="aiItems"></div>
                    <div class="ai-summary-row">
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 16px;">
                            <div id="aiBalanceNote" class="ai-balance-note" role="alert"></div>
                            
                            <div class="card shadow-none mb-0" style="border: 1px solid #f3e7d8; background: #fffaf4; border-radius: 10px;">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-3 fw-bold" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #ff9f43 !important;">Payment Details</h6>
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Payment Mode</label>
                                            <select id="aiPaymentMode" class="form-control">
                                                <option value="pending">Pending</option>
                                                <option value="cash">Cash</option>
                                                <option value="online">Online</option>
                                                <option value="cashonline">Cash + Online</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="aiPaidTypeContainer" style="display: none;">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Paid Type</label>
                                            <select id="aiPaidType" class="form-control">
                                                <option value="full">Fully Paid</option>
                                                <option value="partial">Partially Paid</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="aiCashAmountContainer" style="display: none;">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Cash Amount</label>
                                            <input type="number" id="aiCashAmount" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-4" id="aiOnlineAmountContainer" style="display: none;">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Online Amount</label>
                                            <input type="number" id="aiOnlineAmount" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col-md-4" id="aiPendingAmountContainer" style="display: none;">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Pending Amount</label>
                                            <input type="number" id="aiPendingAmount" class="form-control" placeholder="0" readonly>
                                        </div>
                                        <div class="col-md-4" id="aiBankContainer" style="display: none;">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-label mb-0 text-muted" style="font-size: 13px;">Select Bank</label>
                                                <button type="button" class="btn btn-sm p-0 text-primary" data-bs-toggle="modal" data-dismiss="modal" data-bs-target="#addBankModal" data-target="#addBankModal" style="font-size: 13px; font-weight: 600; background: none; border: none;"><i class="fas fa-plus"></i> Add Bank</button>
                                            </div>
                                            <select id="aiBankId" class="form-control">
                                                <option value="">Select Bank</option>
                                                @foreach ($banks ?? [] as $bank)
                                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label mb-1 text-muted" style="font-size: 13px;">Old Balance Amount</label>
                                            <input type="number" id="aiOldBalance" class="form-control" placeholder="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ai-totals" aria-label="Purchase summary">
                            <div class="ai-total-row"><span>Total items</span><span id="aiItemsCount">0</span></div>
                            <div class="ai-total-row"><span>Gross amount</span><span id="aiGrossTotal">₹0.00</span></div>
                            <div class="ai-total-row"><span>Item discount</span><span id="aiDiscountTotal">₹0.00</span></div>
                            <div class="ai-total-row"><label for="aiInvoiceDiscount">Bill discount</label><input id="aiInvoiceDiscount" type="number" step="0.01" min="0" value="0.00" class="form-control" style="max-width: 130px"></div>
                            <div class="ai-total-row"><span>Taxable amount</span><span id="aiTaxableTotal">₹0.00</span></div>
                            <div class="ai-total-row"><span>CGST</span><span id="aiCgstTotal">₹0.00</span></div>
                            <div class="ai-total-row"><span>SGST</span><span id="aiSgstTotal">₹0.00</span></div>
                            <div class="ai-total-row" id="aiIgstRow"><span>IGST</span><span id="aiIgstTotal">₹0.00</span></div>
                            <div class="ai-total-row"><span>Invoice total</span><span id="aiItemsTotal">₹0.00</span></div>
                            <div class="ai-total-row"><label for="aiInvoiceAdjustment">Rounding off</label><input id="aiInvoiceAdjustment" type="number" step="0.01" value="0.00" class="form-control" style="max-width: 130px"></div>
                            <div class="ai-total-row ai-total-payable"><span>Payable total</span><span id="aiGrandTotal">₹0.00</span></div>
                        </div>
                    </div>
                    
                    <div class="ai-actions d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-cancel" id="aiResetBtn">Upload another</button>
                        <button type="button" class="btn btn-ai-primary" id="aiSaveBtn">
                            Save purchase & update stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="addBankModalLabel" style="font-size: 18px;">Add New Bank</h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addBankForm">
                    <div class="modal-body pb-0 pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 13px;">Bank Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="bank_name" id="newBankName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 13px;">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="account_number" id="newBankAccount" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 13px;">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ifsc_code" id="newBankIfsc" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 13px;">Branch Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="branch_name" id="newBankBranch" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 13px;">Opening Balance</label>
                                <input type="number" class="form-control" name="opening_balance" id="newBankOpeningBalance" value="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-4 pb-3 px-3 justify-content-start">
                        <button type="button" class="btn btn-dark px-4" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-ai-primary px-4" id="saveBankBtn" style="border-radius: 8px; background: #ff9f43; border-color: #ff9f43; color: #fff;">Save Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
(function () {
    const csrf = '{{ csrf_token() }}';
    const vendors = @json($vendors);
    const categories = @json($categories);
    const products = @json($products);
    const fileInput = document.getElementById('aiBillFile');
    const cameraInput = document.getElementById('aiCameraFile');
    const preview = document.getElementById('aiPreview');
    const itemsEl = document.getElementById('aiItems');
    let extractedHeader = {};
    let honorPrintedTotal = true;
    let rowIndex = 0;
    let balanceWarningKey = '';
    let currentLedgerBefore = 0;

    function authHeaders() {
        return {
            'X-CSRF-TOKEN': csrf,
            'Authorization': 'Bearer ' + (localStorage.getItem('authToken') || '')
        };
    }

    function money(n) {
        return '₹' + Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function fillVendorSelect(selectedId) {
        const select = document.getElementById('aiVendor');
        select.innerHTML = '<option value="">Create from bill name</option>';
        vendors.forEach(function (vendor) {
            const opt = document.createElement('option');
            opt.value = vendor.id;
            opt.textContent = vendor.name + (vendor.phone ? ' (' + vendor.phone + ')' : '');
            if (String(vendor.id) === String(selectedId || '')) opt.selected = true;
            select.appendChild(opt);
        });
        document.getElementById('aiVendorName').style.display = select.value ? 'none' : 'block';
    }

    document.getElementById('aiVendor').addEventListener('change', function () {
        document.getElementById('aiVendorName').style.display = this.value ? 'none' : 'block';
        const vendor = vendors.find(v => String(v.id) === String(this.value));
        if (vendor && vendor.phone) {
            document.getElementById('aiVendorPhone').value = vendor.phone;
        }
        checkVendorBalance();
    });

    const aiPaymentMode = document.getElementById('aiPaymentMode');
    const aiPaidType = document.getElementById('aiPaidType');
    const aiCashContainer = document.getElementById('aiCashAmountContainer');
    const aiOnlineContainer = document.getElementById('aiOnlineAmountContainer');
    const aiPendingContainer = document.getElementById('aiPendingAmountContainer');
    const aiBankContainer = document.getElementById('aiBankContainer');
    const aiPaidTypeContainer = document.getElementById('aiPaidTypeContainer');

    function updatePaymentFields() {
        const mode = aiPaymentMode.value;
        const type = aiPaidType.value;
        
        aiCashContainer.style.display = 'none';
        aiOnlineContainer.style.display = 'none';
        aiPendingContainer.style.display = 'none';
        aiBankContainer.style.display = 'none';
        aiPaidTypeContainer.style.display = 'none';

        if (mode === 'cash') {
            aiPaidTypeContainer.style.display = 'block';
            if (type === 'partial') {
                aiCashContainer.style.display = 'block';
                aiPendingContainer.style.display = 'block';
            }
        } else if (mode === 'online') {
            aiPaidTypeContainer.style.display = 'block';
            aiBankContainer.style.display = 'block';
            if (type === 'partial') {
                aiOnlineContainer.style.display = 'block';
                aiPendingContainer.style.display = 'block';
            }
        } else if (mode === 'cashonline') {
            aiPaidTypeContainer.style.display = 'block';
            aiBankContainer.style.display = 'block';
            aiCashContainer.style.display = 'block';
            aiOnlineContainer.style.display = 'block';
            if (type === 'partial') {
                aiPendingContainer.style.display = 'block';
            }
        }
        calculatePending();
    }

    function calculatePending() {
        const total = Number(document.getElementById('aiGrandTotal').innerText.replace(/[^0-9.-]+/g, "")) || 0;
        const mode = aiPaymentMode.value;
        const type = aiPaidType.value;
        let pending = 0;

        const cashAmt = Number(document.getElementById('aiCashAmount').value) || 0;
        const onlineAmt = Number(document.getElementById('aiOnlineAmount').value) || 0;

        if (mode === 'pending') {
            pending = total;
        } else if (type === 'full' && mode !== 'cashonline') {
            pending = 0;
        } else {
            pending = Math.max(0, total - cashAmt - onlineAmt);
        }
        document.getElementById('aiPendingAmount').value = pending.toFixed(2);
    }

    $('#aiPaymentMode').on('change', updatePaymentFields);
    $('#aiPaidType').on('change', updatePaymentFields);
    document.getElementById('aiCashAmount').addEventListener('input', calculatePending);
    document.getElementById('aiOnlineAmount').addEventListener('input', calculatePending);

    async function checkVendorBalance() {
        const note = document.getElementById('aiBalanceNote');
        const vendorId = document.getElementById('aiVendor').value;
        const previous = extractedHeader.previous_balance;
        const current = extractedHeader.current_balance;
        note.style.display = 'none';
        note.innerHTML = '';

        if (!vendorId || (previous === null && current === null)) return;

        const params = new URLSearchParams({
            type: 'vendor', vendor_id: vendorId, month: 'all',
            selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || ''
        });

        try {
            const response = await fetch('/api/get-payment-details?' + params.toString(), {
                headers: Object.assign({ 'Accept': 'application/json' }, authHeaders())
            });
            if (!response.ok) throw new Error('Ledger balance could not be loaded.');
            const data = await response.json();
            if (String(document.getElementById('aiVendor').value) !== String(vendorId)) return;

            const ledgerBefore = (Number(data.openingBalance) || 0) + (data.ledgerEntries || []).reduce(function (balance, entry) {
                return balance + (Number(entry.credit) || 0) - (Number(entry.debit) || 0);
            }, 0);
            currentLedgerBefore = ledgerBefore;
            if (previous !== null && document.getElementById('aiOldBalance').value === '') {
                document.getElementById('aiOldBalance').value = previous;
            } else if (document.getElementById('aiOldBalance').value === '') {
                document.getElementById('aiOldBalance').value = ledgerBefore;
            }
            
            const billTotal = Number(extractedHeader.grand_total) || 0;
            const ledgerAfter = ledgerBefore + billTotal;
            const differences = [];

            if (previous !== null && Math.abs(Number(previous) - ledgerBefore) > 0.01) {
                differences.push(`Opening/previous balance: bill ${money(previous)}, ledger ${money(ledgerBefore)} (difference ${money(Number(previous) - ledgerBefore)})`);
            }
            if (current !== null && Math.abs(Number(current) - ledgerAfter) > 0.01) {
                differences.push(`Closing/current balance: bill ${money(current)}, expected ledger ${money(ledgerAfter)} (difference ${money(Number(current) - ledgerAfter)})`);
            }

            if (!differences.length) return;
            note.innerHTML = '<strong>Vendor balance difference found</strong>' + differences.map(escapeHtml).join('<br>');
            note.style.display = 'block';

            const warningKey = vendorId + '|' + differences.join('|');
            if (warningKey !== balanceWarningKey) {
                balanceWarningKey = warningKey;
                Swal.fire({ icon: 'warning', title: 'Vendor balance difference', html: differences.map(escapeHtml).join('<br>'), confirmButtonColor: '#ff9f43' });
            }
        } catch (error) {
            note.innerHTML = '<strong>Balance check unavailable</strong>' + escapeHtml(error.message);
            note.style.display = 'block';
        }
    }

    const dropzones = document.querySelectorAll('.ai-dropzone');
    ['dragenter', 'dragover'].forEach(function (evt) {
        dropzones.forEach(dz => {
            dz.addEventListener(evt, function (e) {
                e.preventDefault();
                dz.classList.add('is-dragover');
            });
        });
    });
    ['dragleave', 'drop'].forEach(function (evt) {
        dropzones.forEach(dz => {
            dz.addEventListener(evt, function (e) {
                e.preventDefault();
                dz.classList.remove('is-dragover');
            });
        });
    });
    dropzones.forEach(dz => {
        dz.addEventListener('drop', function (e) {
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                if (cameraInput) cameraInput.value = '';
                showPreview();
            }
        });
    });
    
    fileInput.addEventListener('change', function() {
        if (cameraInput) cameraInput.value = '';
        showPreview();
    });
    
    if (cameraInput) {
        cameraInput.addEventListener('change', function() {
            fileInput.value = '';
            showPreview();
        });
    }

    function showPreview() {
        const files = fileInput.files.length ? fileInput.files : (cameraInput && cameraInput.files);
        if (!files || !files.length) return;
        document.getElementById('aiFileName').textContent = files.length === 1 ? files[0].name : files.length + ' files selected';
        const url = URL.createObjectURL(files[0]);
        preview.style.display = 'block';
        preview.querySelector('img').src = url;
        document.getElementById('aiOpenFullPreview').href = url;
        document.getElementById('aiFullPreviewAction').style.display = 'block';
        if (files.length > 1) {
            document.getElementById('aiPreviewCountText').textContent = '(Previewing 1 of ' + files.length + ')';
            document.getElementById('aiPreviewCountText').style.display = 'inline-block';
        } else {
            document.getElementById('aiPreviewCountText').style.display = 'none';
        }
    }

    function categoryOptions(selected) {
        const chosen = selected || 'General';
        const hasGeneral = categories.some(function (cat) {
            return String(cat.name || '').trim().toLowerCase() === 'general';
        });
        let html = '<option value="">Choose category</option>';

        if (!hasGeneral) {
            const sel = String(chosen).toLowerCase() === 'general' ? ' selected' : '';
            html += `<option value="General"${sel}>General</option>`;
        }

        categories.forEach(function (cat) {
            const isGeneralDefault = !selected && String(cat.name || '').trim().toLowerCase() === 'general';
            const sel = String(cat.id) === String(chosen) || isGeneralDefault ? ' selected' : '';
            html += `<option value="${cat.id}"${sel}>${escapeHtml(cat.name)}</option>`;
        });
        return html;
    }

    function productOptions(selectedId, extractedName) {
        let html = `<option value="">New: ${escapeHtml(extractedName || 'product')}</option>`;
        products.forEach(function (p) {
            const sel = String(p.id) === String(selectedId || '') ? ' selected' : '';
            html += `<option value="${p.id}"${sel}>${escapeHtml(p.name)} (stock ${p.quantity})</option>`;
        });
        return html;
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function addItemRow(item) {
        item = item || {};
        const idx = rowIndex++;
        const action = item.product_id ? 'update' : 'create';
        const wrap = document.createElement('div');
        wrap.className = 'ai-item-row ' + (action === 'update' ? 'is-existing' : 'is-new');
        wrap.dataset.index = idx;
        wrap.dataset.billAmount = item.amount > 0 ? String(item.amount) : '';
        wrap.extractionMetadata = item.extraction_metadata || {};
        wrap.innerHTML = `
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="ai-item-index js-item-index">Item</span>
                    <span class="ai-badge ${action === 'update' ? 'ai-badge-update' : 'ai-badge-create'} js-action-badge">
                        ${action === 'update' ? 'Existing item' : 'New item'}
                    </span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-row">Remove</button>
            </div>
            <div class="row g-2">
                <div class="col-6 col-md-4">
                    <label class="form-label">Match / create product</label>
                    <select class="form-control js-product">${productOptions(item.product_id, item.name)}</select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Product name</label>
                    <input type="text" class="form-control js-name" value="${escapeHtml(item.name || '')}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Category</label>
                    <select class="form-control js-category">${categoryOptions(item.category_id)}</select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">HSN</label>
                    <input type="text" class="form-control js-hsn" value="${escapeHtml(item.hsn || '')}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control js-barcode" value="${escapeHtml(item.barcode || '')}" placeholder="Optional">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">MRP</label>
                    <input type="number" step="0.01" min="0" class="form-control js-mrp" value="${item.mrp || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Stock qty</label>
                    <input type="number" step="0.01" min="0.01" class="form-control js-qty" value="${item.quantity || 1}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Rate</label>
                    <input type="number" step="0.01" min="0" class="form-control js-rate" value="${item.rate || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Calculated amount</label>
                    <input type="number" step="0.01" min="0" class="form-control js-amount" value="${item.amount || 0}" readonly>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Free qty</label>
                    <input type="number" step="0.01" min="0" class="form-control js-free-qty" value="${item.free_quantity || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Discount %</label>
                    <input type="number" step="0.01" min="0" class="form-control js-discount-percent" value="${item.discount_percent || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Discount amount</label>
                    <input type="number" step="0.01" min="0" class="form-control js-discount-amount" value="${item.discount_amount || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">GST / tax %</label>
                    <input type="number" step="0.01" min="0" class="form-control js-gst-percent" value="${item.gst_percent || 0}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Purchase type</label>
                    <select class="form-control js-purchase-type"><option value="local" ${item.purchase_type === 'central' ? '' : 'selected'}>Local</option><option value="central" ${item.purchase_type === 'central' ? 'selected' : ''}>Central</option></select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Current stock</label>
                    <input type="text" class="form-control js-stock" value="${item.current_stock || 0}" readonly>
                </div>
            </div>
        `;
        wrap.querySelector('.js-remove-row').addEventListener('click', function () {
            wrap.remove();
            recalcTotals();
        });
        wrap.querySelector('.js-product').addEventListener('change', function () {
            const product = products.find(p => String(p.id) === String(this.value));
            const badge = wrap.querySelector('.js-action-badge');
            if (product) {
                wrap.querySelector('.js-name').value = product.name;
                wrap.querySelector('.js-category').value = product.category_id || wrap.querySelector('.js-category').value;
                wrap.querySelector('.js-stock').value = product.quantity;
                badge.className = 'ai-badge ai-badge-update js-action-badge';
                badge.textContent = 'Existing item';
            } else {
                wrap.querySelector('.js-stock').value = 0;
                badge.className = 'ai-badge ai-badge-create js-action-badge';
                badge.textContent = 'New item';
            }
            refreshItemLabels();
        });
        ['.js-qty', '.js-rate', '.js-discount-percent', '.js-discount-amount'].forEach(function (selector) {
            wrap.querySelector(selector).addEventListener('input', function () {
                wrap.dataset.moneyEdited = '1';
                honorPrintedTotal = false;
                recalcTotals();
            });
        });
        wrap.querySelector('.js-gst-percent').addEventListener('input', recalcTotals);
        const evidence = document.createElement('details');
        const summary = document.createElement('summary');
        summary.textContent = 'Extracted invoice fields';
        evidence.appendChild(summary);
        const fields = Object.assign({}, item.extraction_metadata?.cells || {});
        ['barcode', 'cgst', 'sgst', 'igst', 'taxable_amount'].forEach(key => {
            if (item[key] !== undefined && item[key] !== null) fields[key.replaceAll('_', ' ')] = item[key];
        });
        Object.entries(fields).forEach(([label, value]) => {
            const entry = document.createElement('div');
            entry.textContent = `${label}: ${value ?? ''}`;
            evidence.appendChild(entry);
        });
        wrap.appendChild(evidence);
        itemsEl.appendChild(wrap);
        recalcTotals();
    }

    function round2(num) {
        return Math.round((num + Number.EPSILON) * 100) / 100;
    }

    function refreshItemLabels() {
        const rows = [...itemsEl.querySelectorAll('.ai-item-row')];
        let existing = 0;
        let created = 0;
        rows.forEach(function (row, index) {
            const isNew = !row.querySelector('.js-product').value;
            row.classList.toggle('is-new', isNew);
            row.classList.toggle('is-existing', !isNew);
            const label = row.querySelector('.js-item-index');
            if (label) label.textContent = 'Item ' + (index + 1);
            const badge = row.querySelector('.js-action-badge');
            if (badge) {
                badge.className = 'ai-badge js-action-badge ' + (isNew ? 'ai-badge-create' : 'ai-badge-update');
                badge.textContent = isNew ? 'New item' : 'Existing item';
            }
            if (isNew) created++; else existing++;
        });
        document.getElementById('aiItemsTotalCount').textContent = rows.length;
        document.getElementById('aiItemsExistingCount').textContent = existing;
        document.getElementById('aiItemsNewCount').textContent = created;
        document.getElementById('aiItemsCount').textContent = rows.length;
        filterItemRows();
    }

    function filterItemRows() {
        const input = document.getElementById('aiItemSearch');
        const query = (input && input.value || '').trim().toLowerCase();
        const rows = [...itemsEl.querySelectorAll('.ai-item-row')];
        let shown = 0;
        rows.forEach(function (row) {
            const productSelect = row.querySelector('.js-product');
            const selectedText = productSelect && productSelect.selectedOptions.length ? productSelect.selectedOptions[0].textContent : '';
            const haystack = [
                row.querySelector('.js-name') && row.querySelector('.js-name').value,
                row.querySelector('.js-hsn') && row.querySelector('.js-hsn').value,
                row.querySelector('.js-barcode') && row.querySelector('.js-barcode').value,
                row.querySelector('.js-item-index') && row.querySelector('.js-item-index').textContent,
                row.querySelector('.js-action-badge') && row.querySelector('.js-action-badge').textContent,
                selectedText
            ].join(' ').toLowerCase();
            const match = query === '' || haystack.indexOf(query) !== -1;
            row.style.display = match ? '' : 'none';
            if (match) shown++;
        });
        const count = document.getElementById('aiItemSearchCount');
        const empty = document.getElementById('aiItemSearchEmpty');
        if (count) count.textContent = query === '' ? '' : shown + ' of ' + rows.length;
        if (empty) empty.style.display = query !== '' && shown === 0 ? 'block' : 'none';
    }

    document.getElementById('aiItemSearch').addEventListener('input', filterItemRows);

    function lineGross(row) {
        const qty = parseFloat(row.querySelector('.js-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.js-rate').value) || 0;
        const computed = round2(qty * rate);
        const printed = parseFloat(row.dataset.billAmount || '');
        if (row.dataset.moneyEdited !== '1' && printed > 0) {
            return printed;
        }
        return computed > 0 ? computed : (printed > 0 ? printed : 0);
    }

    function recalcTotals() {
        let gross = 0, itemDiscount = 0, taxable = 0, calculatedTax = 0;
        const rows = [...itemsEl.querySelectorAll('.ai-item-row')];
        const parsedRows = rows.map(function (row) {
            const rowGross = lineGross(row);
            const rowDiscount = parseFloat(row.querySelector('.js-discount-amount').value) || round2(rowGross * ((parseFloat(row.querySelector('.js-discount-percent').value) || 0) / 100));
            return { row, gross: rowGross, discount: Math.min(rowGross, rowDiscount), base: Math.max(0, rowGross - rowDiscount), rate: parseFloat(row.querySelector('.js-gst-percent').value) || 0 };
        });
        const baseBeforeBillDiscount = parsedRows.reduce((sum, item) => sum + item.base, 0);
        const billDiscount = Math.min(baseBeforeBillDiscount, parseFloat(document.getElementById('aiInvoiceDiscount').value) || 0);
        parsedRows.forEach(function (item) {
            const allocatedBillDiscount = round2(baseBeforeBillDiscount > 0 ? billDiscount * (item.base / baseBeforeBillDiscount) : 0);
            const rowTaxable = Math.max(0, item.base - allocatedBillDiscount);
            const isCentral = item.row.querySelector('.js-purchase-type').value === 'central';
            let rowTax = 0;
            if (isCentral) {
                rowTax = round2(rowTaxable * (item.rate / 100));
            } else {
                const cgst = round2(rowTaxable * (item.rate / 200));
                const sgst = round2(rowTaxable * (item.rate / 200));
                rowTax = cgst + sgst;
            }
            item.row.querySelector('.js-amount').value = item.gross.toFixed(2);
            gross += item.gross; itemDiscount += item.discount; taxable += rowTaxable; calculatedTax += rowTax;
        });
        const header = extractedHeader || {};
        const cgst = Number(header.cgst_amount) > 0 ? Number(header.cgst_amount) : (Number(header.igst_amount) > 0 ? 0 : calculatedTax / 2);
        const sgst = Number(header.sgst_amount) > 0 ? Number(header.sgst_amount) : (Number(header.igst_amount) > 0 ? 0 : calculatedTax / 2);
        const igst = Number(header.igst_amount) > 0 ? Number(header.igst_amount) : 0;
        const rounding = Number(document.getElementById('aiInvoiceAdjustment').value) || 0;
        const printedGrand = header.grand_total_extracted !== false ? round2(Number(header.grand_total) || 0) : 0;
        if (honorPrintedTotal && printedGrand > 0 && gross > 0) {
            const targetTaxable = round2(printedGrand - cgst - sgst - igst - rounding);
            const impliedDiscount = round2(gross - billDiscount - targetTaxable);
            if (targetTaxable >= -0.05 && impliedDiscount >= -0.05 && impliedDiscount <= gross + 0.05) {
                itemDiscount = Math.max(0, impliedDiscount);
                taxable = Math.max(0, round2(gross - itemDiscount - billDiscount));
            }
        }
        const invoiceTotal = round2(taxable + cgst + sgst + igst);
        refreshItemLabels();
        document.getElementById('aiGrossTotal').textContent = money(gross);
        document.getElementById('aiDiscountTotal').textContent = money(itemDiscount);
        document.getElementById('aiTaxableTotal').textContent = money(taxable);
        document.getElementById('aiCgstTotal').textContent = money(cgst);
        document.getElementById('aiSgstTotal').textContent = money(sgst);
        document.getElementById('aiIgstTotal').textContent = money(igst);
        document.getElementById('aiIgstRow').style.display = igst > 0 ? '' : 'none';
        document.getElementById('aiItemsTotal').textContent = money(invoiceTotal);
        const adjustment = Number(document.getElementById('aiInvoiceAdjustment').value) || 0;
        document.getElementById('aiGrandTotal').textContent = money(invoiceTotal + adjustment);
        if (typeof calculatePending === 'function') {
            calculatePending();
        }
    }

        document.getElementById('aiInvoiceDiscount').addEventListener('input', function () {
            honorPrintedTotal = false;
            recalcTotals();
        });
        document.getElementById('aiInvoiceAdjustment').addEventListener('input', function () {
            honorPrintedTotal = false;
            recalcTotals();
        });

    document.getElementById('aiAddRow').addEventListener('click', function () {
        addItemRow({ name: '', quantity: 1, rate: 0, amount: 0, action: 'create' });
    });

    document.getElementById('aiExtractBtn').addEventListener('click', async function () {
        const files = fileInput.files.length ? fileInput.files : (cameraInput && cameraInput.files);
        if (!files || !files.length) {
            Swal.fire({ icon: 'warning', title: 'Select a bill image first', confirmButtonColor: '#ff9f43' });
            return;
        }
        const btn = this;
        const original = btn.innerHTML;
        btn.disabled = true;

        const pages = [];

        try {
            for (let i = 0; i < files.length; i++) {
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Reading bill ${i + 1} of ${files.length}...`;
                
                const form = new FormData();
                form.append('bill', files[i]);
                form.append('_token', csrf);

                const res = await fetch('{{ route("purchase.ai-bill.parse") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: form
                });
                
                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(`File ${i + 1}: ` + (data.message || 'Could not read this bill.'));
                }
                pages.push(data);
            }

            let mergedData = pages[0];
            if (pages.length > 1) {
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Combining ${pages.length} pages...`;
                const mergeRes = await fetch('{{ route("purchase.ai-bill.merge") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ pages })
                });
                mergedData = await mergeRes.json();
                if (!mergeRes.ok || !mergedData.success) {
                    throw new Error(mergedData.message || 'Could not combine the bill pages.');
                }
            }

            if (mergedData) {
                fillForm(mergedData);
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Could not read bill', text: err.message, confirmButtonColor: '#ff9f43' });
        } finally {
            btn.innerHTML = original;
            btn.disabled = false;
        }
    });

    function fillForm(data) {
        const header = data.header || {};
        const warnings = (data.warnings || []).concat((data.items || []).flatMap(item => item.warnings || []));
        if (warnings.length) Swal.fire({ icon: 'warning', title: 'Review extracted fields', text: [...new Set(warnings)].join(' ') });
        fillVendorSelect(header.vendor_id);
        extractedHeader = header;
        document.getElementById('aiVendorName').value = header.vendor_name || '';
        document.getElementById('aiVendorPhone').value = header.vendor_phone || '';
        document.getElementById('aiVendorEmail').value = header.vendor_email || '';
        document.getElementById('aiVendorGstin').value = header.vendor_gstin || '';
        document.getElementById('aiVendorAddress').value = header.vendor_address || '';
        document.getElementById('aiBillNo').value = header.bill_no || '';
        document.getElementById('aiBillDate').value = header.bill_date || '';
        document.getElementById('aiRemark').value = [
            header.customer_name ? 'Party: ' + header.customer_name : '',
            header.amount_in_words || ''
        ].filter(Boolean).join(' · ');
        honorPrintedTotal = true;
        document.getElementById('aiInvoiceDiscount').value = Number(header.invoice_discount_amount || ((Number(header.invoice_discount_percent || 0) * Number(header.items_total || 0)) / 100)).toFixed(2);
        document.getElementById('aiInvoiceAdjustment').value = Number(header.rounding_adjustment || 0).toFixed(2);
        document.getElementById('aiItemSearch').value = '';
        itemsEl.innerHTML = '';
        rowIndex = 0;
        (data.items || []).forEach(addItemRow);
        document.getElementById('aiReview').style.display = 'block';
        recalcTotals();
        
        let statusStr = header.payment_status || 'unpaid';
        let modeStr = (header.payment_mode || '').toLowerCase();
        
        if (statusStr === 'unpaid') {
            document.getElementById('aiPaymentMode').value = 'pending';
        } else {
            if (modeStr.includes('cash')) {
                document.getElementById('aiPaymentMode').value = 'cash';
            } else {
                document.getElementById('aiPaymentMode').value = 'online';
            }
            document.getElementById('aiPaidType').value = 'full';
        }
        $('#aiPaymentMode').trigger('change');
        $('#aiPaidType').trigger('change');

        recalcTotals();
        checkVendorBalance();
        document.getElementById('aiReview').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('aiResetBtn').addEventListener('click', function () {
        document.getElementById('aiReview').style.display = 'none';
        fileInput.value = '';
        if (cameraInput) cameraInput.value = '';
        preview.style.display = 'none';
        document.getElementById('aiFullPreviewAction').style.display = 'none';
        document.getElementById('aiPreviewCountText').style.display = 'none';
        document.getElementById('aiFileName').textContent = '';
        document.getElementById('aiBalanceNote').style.display = 'none';
        balanceWarningKey = '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('aiSaveBtn').addEventListener('click', async function () {
        const btn = this;
        const original = btn.innerHTML;
        const billNo = document.getElementById('aiBillNo').value.trim();
        const billDate = document.getElementById('aiBillDate').value;
        const vendorSelect = document.getElementById('aiVendor').value;
        const vendorName = document.getElementById('aiVendorName').value.trim();
        document.getElementById('aiBillNoError').textContent = '';

        if (!billNo) {
            document.getElementById('aiBillNoError').textContent = 'Bill number is required.';
            return;
        }
        if (!billDate) {
            Swal.fire({ icon: 'warning', title: 'Purchase date is required', confirmButtonColor: '#ff9f43' });
            return;
        }
        if (!vendorSelect && !vendorName) {
            Swal.fire({ icon: 'warning', title: 'Select or enter a vendor', confirmButtonColor: '#ff9f43' });
            return;
        }

        const productsPayload = [];
        const rowsForSave = [...itemsEl.querySelectorAll('.ai-item-row')];
        const lineBases = rowsForSave.map(function (row) {
            const rowGross = lineGross(row);
            const itemDiscount = parseFloat(row.querySelector('.js-discount-amount').value) || round2(rowGross * ((parseFloat(row.querySelector('.js-discount-percent').value) || 0) / 100));
            return Math.max(0, rowGross - itemDiscount);
        });
        const totalLineBase = lineBases.reduce((sum, value) => sum + value, 0);
        const billDiscount = Math.min(totalLineBase, parseFloat(document.getElementById('aiInvoiceDiscount').value) || 0);
        rowsForSave.forEach(function (row, rowPosition) {
            const qty = parseFloat(row.querySelector('.js-qty').value) || 0;
            const rate = parseFloat(row.querySelector('.js-rate').value) || 0;
            const rowGross = lineGross(row);
            const itemDiscount = parseFloat(row.querySelector('.js-discount-amount').value) || round2(rowGross * ((parseFloat(row.querySelector('.js-discount-percent').value) || 0) / 100));
            const billDiscountShare = round2(totalLineBase > 0 ? billDiscount * (lineBases[rowPosition] / totalLineBase) : 0);
            const discountAmount = itemDiscount + billDiscountShare;
            const taxableAmount = Math.max(0, rowGross - discountAmount);
            const gstRate = parseFloat(row.querySelector('.js-gst-percent').value) || 0;
            const isCentral = row.querySelector('.js-purchase-type').value === 'central';
            
            let gstAmount, cgst, sgst;
            if (isCentral) {
                gstAmount = round2(taxableAmount * (gstRate / 100));
                cgst = 0;
                sgst = 0;
            } else {
                cgst = round2(taxableAmount * (gstRate / 200));
                sgst = round2(taxableAmount * (gstRate / 200));
                gstAmount = cgst + sgst;
            }
            
            const amount = taxableAmount + gstAmount;
            const name = row.querySelector('.js-name').value.trim();
            const productId = row.querySelector('.js-product').value;
            if (!name || qty <= 0) return;
            if (!row.querySelector('.js-category').value) {
                row.querySelector('.js-category').focus();
                return;
            }
            const unitPrice = rate;
            productsPayload.push({
                extraction_metadata: row.extractionMetadata,
                id: productId || name,
                category_id: row.querySelector('.js-category').value,
                price: unitPrice,
                quantity: qty,
                discount_percent: parseFloat(row.querySelector('.js-discount-percent').value) || 0,
                discount_amount: discountAmount,
                free_quantity: parseFloat(row.querySelector('.js-free-qty').value) || 0,
                purchase_type: row.querySelector('.js-purchase-type').value,
                total: amount,
                hsn_code: row.querySelector('.js-hsn').value.trim(),
                barcode: row.querySelector('.js-barcode').value.trim(),
                mrp: parseFloat(row.querySelector('.js-mrp').value) || 0,
                product_gst_total: gstAmount,
                product_gst_details: JSON.stringify(isCentral 
                    ? [{ name: 'IGST', rate: gstRate, amount: gstAmount }]
                    : [{ name: 'CGST', rate: gstRate / 2, amount: cgst }, { name: 'SGST', rate: gstRate / 2, amount: sgst }]),
                imei_no: '[]'
            });
        });

        if (!productsPayload.length) {
            Swal.fire({ icon: 'warning', title: 'Add at least one product line', confirmButtonColor: '#ff9f43' });
            return;
        }

        const confirmSave = await Swal.fire({
            title: 'Save this purchase?',
            text: 'Existing products will get stock. Missing products will be created.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff9f43',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, save'
        });
        if (!confirmSave.isConfirmed) return;

        const adjustment = Number(document.getElementById('aiInvoiceAdjustment').value) || 0;
        const shownGrand = Number(String(document.getElementById('aiGrandTotal').innerText).replace(/[^0-9.-]+/g, '')) || 0;
        const grandTotal = shownGrand > 0 ? shownGrand : +(productsPayload.reduce((sum, row) => sum + Number(row.total || 0), 0) + adjustment).toFixed(2);
        extractedHeader.invoice_adjustment = adjustment;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        btn.style.pointerEvents = 'none';

        fetch('/api/purchase_order', {
            method: 'POST',
            headers: Object.assign({ 'Content-Type': 'application/json', 'Accept': 'application/json' }, authHeaders()),
            body: JSON.stringify({
                extraction_metadata: extractedHeader,
                vendor_id: vendorSelect || vendorName,
                vendor_phone: document.getElementById('aiVendorPhone').value.trim() || null,
                vendor_email: document.getElementById('aiVendorEmail').value.trim() || null,
                vendor_gstin: document.getElementById('aiVendorGstin').value.trim() || null,
                vendor_address: document.getElementById('aiVendorAddress').value.trim() || null,
                status: document.getElementById('aiStatus').value,
                discount: parseFloat(document.getElementById('aiInvoiceDiscount').value) || 0,
                shipping: 0,
                grand_total: grandTotal,
                products: productsPayload,
                taxes: [],
                gst_option: 'with',
                bill_no: billNo,
                purchase_date: billDate,
                remark: document.getElementById('aiRemark').value.trim(),
                payment_mode: document.getElementById('aiPaymentMode').value,
                paid_type: document.getElementById('aiPaymentMode').value !== 'pending' ? document.getElementById('aiPaidType').value : '',
                cash_amount: document.getElementById('aiCashAmount').value,
                upi_amount: document.getElementById('aiOnlineAmount').value,
                bank_id: document.getElementById('aiBankId').value,
                old_balance: document.getElementById('aiOldBalance').value,
                selectedSubAdminId: localStorage.getItem('selectedSubAdminId') || null
            })
        }).then(async function (res) {
            const data = await res.json();
            if (!res.ok || !data.success) {
                const firstError = data.errors ? Object.values(data.errors)[0] : null;
                throw new Error((firstError && firstError[0]) || data.message || 'Save failed.');
            }
            await Swal.fire({ icon: 'success', title: 'Purchase saved', text: data.message || 'Stock updated.', confirmButtonColor: '#ff9f43' });
            window.location.href = data.purchase_id ? '/print-purchase/' + data.purchase_id : '{{ route("purchase.lists") }}';
        }).catch(function (err) {
            if (String(err.message).toLowerCase().includes('bill number')) {
                document.getElementById('aiBillNoError').textContent = err.message;
            }
            Swal.fire({ icon: 'error', title: 'Could not save', text: err.message, confirmButtonColor: '#ff9f43' });
        }).finally(function () {
            btn.innerHTML = original;
            btn.style.pointerEvents = 'auto';
        });
    });

    fillVendorSelect(null);

    // Add New Bank AJAX
    document.getElementById('addBankForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('saveBankBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
        btn.disabled = true;

        const formData = new FormData(this);
        if (!formData.get('status')) formData.append('status', '1');
        const selectedSubAdminId = localStorage.getItem('selectedSubAdminId');
        if (selectedSubAdminId) formData.append('selectedSubAdminId', selectedSubAdminId);

        fetch('/api/banks', {
            method: 'POST',
            headers: Object.assign({
                'Accept': 'application/json'
            }, authHeaders()),
            body: formData
        }).then(async function (res) {
            const result = await res.json();
            if (!res.ok || !result.status) {
                let errorMsg = 'Could not save bank.';
                if (result.errors) {
                    errorMsg = Object.values(result.errors)[0][0] || errorMsg;
                } else if (result.message) {
                    errorMsg = result.message;
                }
                throw new Error(errorMsg);
            }
            
            // Add to dropdown and select it
            const select = document.getElementById('aiBankId');
            const opt = document.createElement('option');
            opt.value = result.data.id;
            opt.textContent = result.data.bank_name;
            select.appendChild(opt);
            select.value = result.data.id;

            // Close modal and reset form
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalEl = document.getElementById('addBankModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            } else if (typeof $ !== 'undefined') {
                $('#addBankModal').modal('hide');
            }
            
            document.getElementById('addBankForm').reset();
            Swal.fire({ icon: 'success', title: 'Success', text: 'Bank added successfully!', confirmButtonColor: '#ff9f43', timer: 1500 });
        }).catch(function (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonColor: '#ff9f43' });
        }).finally(function () {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
})();
</script>
@endpush
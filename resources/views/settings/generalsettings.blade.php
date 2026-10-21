@extends('layout.app')

@section('title', 'Shop Settings')

@section('content')
    <style>
        @media screen and (max-width: 768px) {
            .form-group {
                margin-bottom: 15px !important;
            }

            .image-upload input[type=file] {
                height: 115px !important;
            }
        }

        .settings-tabs {
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e6eaf0 !important;
            border-radius: 8px;
            display: inline-flex;
            margin-bottom: 18px;
            padding: 4px;
        }

        .settings-tabs .nav-item {
            margin: 0;
        }

        .settings-tabs .nav-link {
            align-items: center;
            background: transparent;
            border: 0 !important;
            border-radius: 6px;
            color: #667085;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 7px;
            line-height: 1.2;
            min-height: 36px;
            padding: 9px 14px;
            transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }



        .settings-tabs .nav-link i {
            color: #98a2b3;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .settings-tabs .nav-link:hover {
            background: #fff;
            color: #1f2937;
        }

        .settings-tabs .nav-link.active {
            background: #fff;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1);
            color: #ff9f43;
        }

        .settings-tabs .nav-link.active i {
            color: #ff9f43;
        }

        .settings-tabs .nav-link.active::before {
            color: #ff9f43;
        }

        .delivery-terms-actions {
            margin-top: 18px;
        }

        .delivery-terms-actions .btn-delivery-update {
            background: #ff9f43;
            border: 1px solid #ff9f43;
            border-radius: 4px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            min-width: 120px;
            padding: 12px 22px;
            transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .delivery-terms-actions .btn-delivery-update:hover,
        .delivery-terms-actions .btn-delivery-update:focus {
            background: #f68b1f;
            border-color: #f68b1f;
            color: #fff;
            box-shadow: 0 6px 18px rgba(255, 159, 67, 0.25);
        }

        #settingsTabsContent {
            margin-top: 0 !important;
        }

        @media screen and (max-width: 575px) {
            .settings-tabs {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .settings-tabs .nav-link {
                justify-content: flex-start;
                width: 100%;
            }
        }

        .dashboard-mobile-subsection-tabs {
            display: none;
        }

        @media screen and (max-width: 767px) {
            .dashboard-mobile-subsection-tabs {
                background: #f8fafc;
                border: 1px solid #e6eaf0;
                border-radius: 8px;
                display: grid;
                gap: 6px;
                grid-template-columns: repeat(3, 1fr);
                padding: 4px;
            }

            .dashboard-mobile-subsection-tab {
                background: transparent;
                border: 0;
                border-radius: 6px;
                color: #667085;
                font-size: 12px;
                font-weight: 600;
                min-height: 34px;
                padding: 7px 6px;
            }

            .dashboard-mobile-subsection-tab.active {
                background: #fff;
                box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1);
                color: #ff9f43;
            }

            #dashboard-settings .dashboard-mobile-panel:not(.active) {
                display: none !important;
            }
        }

        /* Modern Multi-step Form Styles */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: flex;
            /* Ensure it's flex as it's a row */
        }

        .mobile-steps {
            margin-bottom: 25px;
        }

        .step-btns-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            background: #f8f9fa;
            padding: 5px;
            border-radius: 50px;
            border: 1px solid #e8ebed;
            overflow-x: auto;
            white-space: nowrap;
        }

        .step-btns-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .step-btns-wrapper::-webkit-scrollbar-thumb {
            background-color: #ddd;
            border-radius: 4px;
        }

        .step-btn {
            background: transparent;
            color: #637381;
            border-radius: 50px;
            flex: 1;
            font-size: 13px;
            padding: 10px 15px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .step-btn.active {
            background: #ff9f43;
            color: #fff;
            box-shadow: 0 4px 10px rgba(255, 159, 67, 0.2);
        }

        #shop-settings:not(.active) {
            display: none !important;
        }

        #shop-settings.active {
            display: block;
        }

        /* Dark Mode Overrides for Tabs and Steps */
        :root[data-theme="dark"] .settings-tabs {
            background: #0f172a;
            border-color: #334155 !important;
        }

        :root[data-theme="dark"] .settings-tabs .nav-link {
            color: #d1d5db;
        }

        :root[data-theme="dark"] .settings-tabs .nav-link:hover {
            background: #0f172a;
            color: #fff;
        }

        :root[data-theme="dark"] .settings-tabs .nav-link.active {
            background: #0f172a;
            color: #ff9f43;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        :root[data-theme="dark"] .step-btns-wrapper {
            background: #0f172a;
            border-color: #334155;
        }

        :root[data-theme="dark"] .step-btn {
            color: #d1d5db;
        }

        :root[data-theme="dark"] .step-btn.active {
            background: #ff9f43;
            color: #fff;
        }
    </style>

    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Shop & Company Settings</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('auth.profile') }}" class="btn btn-added"><i class="fas fa-eye me-1"></i>View Profile</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="shop-tab" data-bs-toggle="tab" data-bs-target="#shop-settings"
                            type="button" role="tab" aria-controls="shop-settings" aria-selected="true">
                            🏪 Shop Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rules-tab" data-bs-toggle="tab" data-bs-target="#company-rules"
                            type="button" role="tab" aria-controls="company-rules" aria-selected="false">
                            🏢 Company Rules
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#delivery-terms"
                            type="button" role="tab" aria-controls="delivery-terms" aria-selected="false">
                            📄 Delivery Terms & Conditions
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="dashboard-tab" data-bs-toggle="tab"
                            data-bs-target="#dashboard-settings" type="button" role="tab"
                            aria-controls="dashboard-settings" aria-selected="false">
                            📊 Dashboard
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="eway-tab" data-bs-toggle="tab" data-bs-target="#eway-settings"
                            type="button" role="tab" aria-controls="eway-settings" aria-selected="false">
                            🚚 E-Way Bill Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="einvoice-tab" data-bs-toggle="tab" data-bs-target="#einvoice-settings"
                            type="button" role="tab" aria-controls="einvoice-settings" aria-selected="false">
                            🧾 E-Invoice Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-settings"
                            type="button" role="tab" aria-controls="sales-settings" aria-selected="false">
                            💰 Sales Setting
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="barcode-tab" data-bs-toggle="tab" data-bs-target="#barcode-settings"
                            type="button" role="tab" aria-controls="barcode-settings" aria-selected="false">
                            <i class="fa fa-barcode me-1"></i> Barcode Setting
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-4" id="settingsTabsContent">
                    <!-- ================= SHOP SETTINGS TAB ================= -->
                    <div class="tab-pane fade show active" id="shop-settings" role="tabpanel" aria-labelledby="shop-tab">
                        <div class="mobile-steps">
                            <div class="step-btns-wrapper">
                                <button type="button" class="step-btn active" data-step="1">Basic Information</button>
                                <button type="button" class="step-btn" data-step="2">Bank Details</button>
                                <button type="button" class="step-btn" data-step="3">System Preferences</button>
                                <button type="button" class="step-btn" data-step="4">Media & Branding</button>
                            </div>
                        </div>

                        <!-- Step 1: Basic Information -->
                        <div class="step-content step-1 active row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Name<span class="manitory">*</span></label>
                                    <input type="text" id="shop_name" placeholder="Enter Title">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Email<span class="manitory">*</span></label>
                                    <input type="text" id="email" placeholder="Enter email">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Phone<span class="manitory">*</span></label>
                                    <input type="text" id="phone" placeholder="Enter Phone">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>State Code</label>
                                    <input type="text" id="state_code" placeholder="Enter State Code">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>GST Number</label>
                                    <input type="text" id="gst_num" placeholder="Enter GST Number">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>CIN Number</label>
                                    <input type="text" id="cin_no" placeholder="Enter CIN Number">
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Shop Address<span class="manitory">*</span></label>
                                    <textarea id="address" placeholder="Enter Address" rows="1" class="form-control"></textarea>
                                    <div class="d-flex justify-content-between align-items-start mt-1">
                                        <small id="coordinateLookupStatus" class="text-muted"
                                            style="line-height:1.2;"></small>
                                        <button type="button" class="btn btn-sm btn-primary ms-2 flex-shrink-0"
                                            id="btnFetchCoordinates" style="padding: 2px 8px; font-size: 11px;">Get GPS
                                            from Address</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Bank Details -->
                        <div class="step-content step-2 row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Bank Name<span class="manitory">*</span></label>
                                    <input type="text" id="bank_name" placeholder="Enter Bank Name">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Branch<span class="manitory">*</span></label>
                                    <input type="text" id="branch" placeholder="Enter Branch">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>A/C No.<span class="manitory">*</span></label>
                                    <input type="number" id="ac_no" class="form-control"
                                        placeholder="Enter A/C No.">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>IFSC Code<span class="manitory">*</span></label>
                                    <input type="text" id="ifsc_code" placeholder="Enter IFSC Code">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: System Preferences -->
                        <div class="step-content step-3 row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Currency Symbol <span class="manitory">*</span></label>
                                    <select id="currency_symbol" class="form-select">
                                        <option value="₹" selected>₹ (Indian Rupee)</option>
                                        <option value="$">$ (US Dollar)</option>
                                        <option value="€">€ (Euro)</option>
                                        <option value="£">£ (British Pound)</option>
                                        <option value="¥">¥ (Japanese Yen)</option>
                                        <option value="₩">₩ (South Korean Won)</option>
                                        <option value="₽">₽ (Russian Ruble)</option>
                                        <option value="₺">₺ (Turkish Lira)</option>
                                        <option value="₫">₫ (Vietnamese Dong)</option>
                                        <option value="฿">฿ (Thai Baht)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label for="currency_position" class="form-label">Currency Symbol Position</label>
                                    <select class="form-select" id="currency_position" name="currency_position" required>
                                        <option value="left">Left (e.g. ₹100 or $100)</option>
                                        <option value="right">Right (e.g. 100₹ or 100$)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Low Stock Warning Quantity</label>
                                    <input type="number" id="low_stock" class="form-control"
                                        placeholder="Enter Low Stock Warning Quantity">
                                    <small id="lowStockError" class="text-danger d-none">Low Stock Quantity cannot be
                                        negative.</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Invoice Size</label>
                                    <select id="invoice_size" name="invoice_size" class="form-select">
                                        <option value="small"
                                            {{ old('invoice_size', $setting->invoice_size ?? 'big') == 'small' ? 'selected' : '' }}>
                                            Small Size Invoice</option>
                                        <option value="big"
                                            {{ old('invoice_size', $setting->invoice_size ?? 'big') == 'big' ? 'selected' : '' }}>
                                            Big Size Invoice</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Send Mail</label>
                                    <select id="send_mail" name="send_mail" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Customer WhatsApp Message</label>
                                    <select id="customer_whatsapp_message" name="customer_whatsapp_message"
                                        class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Admin WhatsApp Message</label>
                                    <select id="admin_whatsapp_message" name="admin_whatsapp_message"
                                        class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Latitude</label>
                                    <input type="text" id="office_latitude" class="form-control"
                                        placeholder="e.g. 21.1268432">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Longitude</label>
                                    <input type="text" id="office_longitude" class="form-control"
                                        placeholder="e.g. 73.1051204">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Radius (m)</label>
                                    <input type="number" id="office_radius" class="form-control" placeholder="e.g. 200"
                                        min="0" value="200">
                                    <small class="text-muted">Staff must be within this distance from office GPS to clock
                                        in.</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Meeting / Follow-up reminder (hours before)</label>
                                    <input type="number" id="appointment_reminder_hours_before"
                                        name="appointment_reminder_hours_before" class="form-control"
                                        placeholder="e.g. 3" min="1">
                                    <small class="text-muted">IST. Default 3.</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Variant Product</label>
                                    <select id="enable_variant_product" name="enable_variant_product" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                    <small class="text-muted">On = Show Variant Product option. Off = Normal Product only.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Media & Branding -->
                        <div class="step-content step-4 row">
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Logo</label>
                                    <div class="image-upload">
                                        <input type="file" id="logo" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="logo_preview" src="" alt="Logo Preview"
                                        style="display:none; max-width: 100px; margin-top: 10px;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6 d-none">
                                <div class="form-group">
                                    <label>Shop Favicon</label>
                                    <div class="image-upload">
                                        <input type="file" id="favicon" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="favicon_preview" src="" alt="Favicon Preview"
                                        style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop QR Code Image</label>
                                    <div class="image-upload">
                                        <input type="file" id="qr_code" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}"
                                                alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="qr_preview" src="" alt="QR Preview"
                                        style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 align-items-center">
                            <div class="col-6">
                                <a href="javascript:void(0);" class="btn btn-submit btn-prev d-none"><i
                                        class="fas fa-arrow-left me-1"></i> Previous</a>
                            </div>
                            <div class="col-6 d-flex justify-content-end gap-2">
                                <a href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
                                <a href="javascript:void(0);" class="btn btn-submit btn-next">Next <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                                <a href="javascript:void(0);" class="btn btn-submit btn-submit-mobile d-none">Submit</a>
                                <button type="button" id="btn-setting-submit" class="d-none">Submit</button>
                            </div>
                        </div>
                    </div>

                    <!-- ================= COMPANY RULES TAB ================= -->
                    <div class="tab-pane fade" id="company-rules" role="tabpanel" aria-labelledby="rules-tab">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Working Hours (per day)<span class="manitory">*</span></label>
                                    <input type="number" id="working_hours" class="form-control" placeholder="e.g. 8"
                                        step="0.5" min="1">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Sunday Off?</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sunday_off"
                                                id="sunday_yes" value="yes">
                                            <label class="form-check-label" for="sunday_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sunday_off"
                                                id="sunday_no" value="no" checked>
                                            <label class="form-check-label" for="sunday_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Saturday Off?</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="saturday_off"
                                                id="saturday_yes" value="yes">
                                            <label class="form-check-label" for="saturday_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="saturday_off"
                                                id="saturday_no" value="no" checked>
                                            <label class="form-check-label" for="saturday_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Grace Period (minutes)<span class="manitory">*</span></label>
                                    <input type="number" id="grace_period" class="form-control" placeholder="e.g. 10"
                                        min="0">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Lunch Break</label>
                                    <input type="number" id="lunch_break" class="form-control"
                                        placeholder="Enter minutes">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Company Open Time<span class="manitory">*</span></label>
                                    <input type="time" id="open_time" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Company Close Time<span class="manitory">*</span></label>
                                    <input type="time" id="close_time" class="form-control">
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Overtime After (hours)</label>
                                    <input type="number" step="0.5" id="overtime_after_hours" class="form-control"
                                        placeholder="e.g. 9">
                                    <small class="text-muted">Hours after which overtime starts (e.g. 9 for 8h shift + 1h
                                        grace)</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Tax Deduction Amount<span class="manitory">*</span></label>
                                    <input type="number" step="0.01" id="tax_deduction_amount" class="form-control"
                                        placeholder="e.g. 200" value="0">
                                    <small class="text-muted">Tax deduction will be applied only when the salary amount
                                        exceeds <span id="salary_exceeds_text">14000</span>.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <div class="form-group">
                                    <label>Salary Amount Exceeds<span class="manitory">*</span></label>
                                    <input type="number" step="0.01" id="salary_exceeds_amount" class="form-control"
                                        placeholder="e.g. 14000" value="0">
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <h5>Security Settings</h5>
                                <hr>
                            </div>

                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label>Location Check on Login</label>
                                    <div class="d-flex align-items-center gap-3 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="location_check_enabled"
                                                id="location_check_on" value="1">
                                            <label class="form-check-label" for="location_check_on">ON (Check GPS
                                                location)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="location_check_enabled"
                                                id="location_check_off" value="0" checked>
                                            <label class="form-check-label" for="location_check_off">OFF (Allow
                                                anywhere)</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">If ON, staff must be within the radius of their assigned
                                        office location to log in and clock in.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveCompanyRules">Save
                                    Rules</a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= DELIVERY TERMS TAB ================= -->
                    <div class="tab-pane fade" id="delivery-terms" role="tabpanel" aria-labelledby="terms-tab">
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="terms_condition_eng">Delivery Terms & Conditions (English)</label>
                                    <textarea id="terms_condition_eng" rows="8" class="form-control"
                                        placeholder="Enter English delivery terms and conditions"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="terms_condition_guj">Delivery Terms & Conditions (Gujarati)</label>
                                    <textarea id="terms_condition_guj" rows="8" class="form-control"
                                        placeholder="Enter Gujarati delivery terms and conditions"></textarea>
                                </div>
                            </div>
                            <div class="col-12 delivery-terms-actions">
                                <button type="button" class="btn btn-delivery-update" id="saveDeliveryTerms">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ================= DASHBOARD SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="dashboard-settings" role="tabpanel" aria-labelledby="dashboard-tab">
                        <div class="row">
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>CRM Section On Dashboard</label>
                                    <select id="show_crm_dashboard" name="show_crm_dashboard" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                    <small class="text-muted">When disabled, the entire CRM section is hidden from the
                                        dashboard.</small>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>HR Portal Section On Dashboard</label>
                                    <select id="show_hr_dashboard" name="show_hr_dashboard" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                    <small class="text-muted">When disabled, only the staff, attendance, and salary section
                                        is hidden.</small>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6">
                                <div class="form-group">
                                    <label>ERP Section On Dashboard</label>
                                    <select id="show_erp_dashboard" name="show_erp_dashboard" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                    <small class="text-muted">When disabled, the entire ERP section is hidden from the
                                        dashboard.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3 dashboard-mobile-subsection-tabs" role="tablist"
                                aria-label="Dashboard subsections">
                                <button type="button" class="dashboard-mobile-subsection-tab active"
                                    data-dashboard-subsection="crm">CRM</button>
                                <button type="button" class="dashboard-mobile-subsection-tab"
                                    data-dashboard-subsection="hr">HR</button>
                                <button type="button" class="dashboard-mobile-subsection-tab"
                                    data-dashboard-subsection="erp">ERP</button>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-3">CRM Dashboard Subsections</h5>
                                <p class="text-muted small mb-3">Control visibility of individual CRM dashboard components:
                                </p>
                            </div>

                            <!-- Top 4 Metric Boxes -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Lead Pipeline Box</label>
                                    <select id="show_crm_lead_pipeline" name="show_crm_lead_pipeline"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Conversion Box</label>
                                    <select id="show_crm_conversion" name="show_crm_conversion" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Follow-up Load Box</label>
                                    <select id="show_crm_followup_load" name="show_crm_followup_load"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Meeting Momentum Box</label>
                                    <select id="show_crm_meeting_momentum" name="show_crm_meeting_momentum"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Charts and Tables -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Lead Status Mix Chart</label>
                                    <select id="show_crm_lead_status_mix" name="show_crm_lead_status_mix"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>CRM Activity Trend Chart</label>
                                    <select id="show_crm_activity_trend" name="show_crm_activity_trend"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Pipeline Quality Table</label>
                                    <select id="show_crm_pipeline_quality" name="show_crm_pipeline_quality"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Recent Leads Table</label>
                                    <select id="show_crm_recent_leads" name="show_crm_recent_leads" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Next 7 Days Table</label>
                                    <select id="show_crm_next_7_days" name="show_crm_next_7_days" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-3">HR Dashboard Subsections</h5>
                                <p class="text-muted small mb-3">Control visibility of individual HR dashboard components:
                                </p>
                            </div>

                            <!-- HR Metric Boxes -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Staff Strength Box</label>
                                    <select id="show_hr_staff_strength" name="show_hr_staff_strength"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Active Staff Box</label>
                                    <select id="show_hr_active_staff" name="show_hr_active_staff" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Monthly Attendance Box</label>
                                    <select id="show_hr_monthly_attendance" name="show_hr_monthly_attendance"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Personal Progress Box</label>
                                    <select id="show_hr_personal_progress" name="show_hr_personal_progress"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- HR Charts and Tables -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>7 Day Attendance Pattern Chart</label>
                                    <select id="show_hr_attendance_pattern" name="show_hr_attendance_pattern"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Salary Payroll Trend Chart</label>
                                    <select id="show_hr_salary_payroll_trend" name="show_hr_salary_payroll_trend"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Payroll Snapshot Table</label>
                                    <select id="show_hr_payroll_snapshot" name="show_hr_payroll_snapshot"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Attendance Watch Table</label>
                                    <select id="show_hr_attendance_watch" name="show_hr_attendance_watch"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Payroll Status Table</label>
                                    <select id="show_hr_payroll_status" name="show_hr_payroll_status"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4 mb-3">
                                <h5 class="mb-3">ERP Dashboard Subsections</h5>
                                <p class="text-muted small mb-3">Control visibility of individual ERP dashboard components:
                                </p>
                            </div>

                            <!-- ERP Top Metric Boxes -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Total Sales Amount Box</label>
                                    <select id="show_erp_total_sales" name="show_erp_total_sales" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Total Purchase Amount Box</label>
                                    <select id="show_erp_total_purchase" name="show_erp_total_purchase"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Total Expense Amount Box</label>
                                    <select id="show_erp_total_expense" name="show_erp_total_expense"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ERP Count Boxes -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Sales Invoice Count Box</label>
                                    <select id="show_erp_sales_invoice_count" name="show_erp_sales_invoice_count"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Purchase Invoice Count Box</label>
                                    <select id="show_erp_purchase_invoice_count" name="show_erp_purchase_invoice_count"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Customers Count Box</label>
                                    <select id="show_erp_customers_count" name="show_erp_customers_count"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Vendors Count Box</label>
                                    <select id="show_erp_vendors_count" name="show_erp_vendors_count"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ERP Charts -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Sales Chart</label>
                                    <select id="show_erp_sales_chart" name="show_erp_sales_chart" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Purchase Chart</label>
                                    <select id="show_erp_purchase_chart" name="show_erp_purchase_chart"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ERP Tables -->
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Recent Sales Table</label>
                                    <select id="show_erp_recent_sales" name="show_erp_recent_sales" class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Recent Purchases Table</label>
                                    <select id="show_erp_recent_purchases" name="show_erp_recent_purchases"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Recent Products Table</label>
                                    <select id="show_erp_recent_products" name="show_erp_recent_products"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Products Delivery Table</label>
                                    <select id="show_erp_products_delivery" name="show_erp_products_delivery"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Recent Rental Table</label>
                                    <select id="show_erp_recent_rentals" name="show_erp_recent_rentals"
                                        class="form-select">
                                        <option value="1">Enable</option>
                                        <option value="0">Disable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveDashboardSettings">
                                    Save Dashboard Settings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= E-WAY BILL SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="eway-settings" role="tabpanel" aria-labelledby="eway-tab">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group d-flex align-items-center gap-3">
                                    <label class="mb-0">Enable E-Way Bill</label>
                                    <div class="form-check form-switch custom-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="enable_eway_bill"
                                            name="enable_eway_bill" value="1">
                                        <label class="form-check-label" for="enable_eway_bill">Enable E-Way Bill</label>
                                    </div>
                                </div>
                            </div>

                            <div id="eway_fields_container" class="row m-0 p-0 w-100" style="display: none;">

                                <div class="col-lg-12 mt-4">
                                    <h5>Section 1 — Company / GSTIN Details</h5>
                                    <hr>
                                </div>

                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>GSTIN Number</label>
                                        <input type="text" id="eway_gstin_number" class="form-control"
                                            placeholder="Enter GSTIN Number">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Legal Business Name</label>
                                        <input type="text" id="eway_legal_business_name" class="form-control"
                                            placeholder="Legal Business Name">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>PAN Number</label>
                                        <input type="text" id="eway_pan_number" class="form-control"
                                            placeholder="Enter PAN Number">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" id="eway_state" class="form-control"
                                            placeholder="Enter State">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>State Code</label>
                                        <input type="text" id="eway_state_code" class="form-control"
                                            placeholder="Enter State Code">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Address Line 1</label>
                                        <input type="text" id="eway_address_line_1" class="form-control"
                                            placeholder="Address Line 1">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Address Line 2</label>
                                        <input type="text" id="eway_address_line_2" class="form-control"
                                            placeholder="Address Line 2">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" id="eway_city" class="form-control"
                                            placeholder="Enter City">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>PIN Code</label>
                                        <input type="text" id="eway_pin_code" class="form-control"
                                            placeholder="Enter PIN Code">
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-4">
                                    <h5>Section 2 — API / GSP Credentials</h5>
                                    <hr>
                                </div>

                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>API Username</label>
                                        <input type="text" id="eway_api_username" class="form-control"
                                            placeholder="Enter API Username">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>API Password</label>
                                        <input type="password" id="eway_api_password" class="form-control"
                                            placeholder="Enter API Password">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>App Key / Client ID</label>
                                        <input type="text" id="eway_app_key_client_id" class="form-control"
                                            placeholder="Enter App Key / Client ID">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveEwayBillSettings">Save
                                    Settings</a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= E-INVOICE SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="einvoice-settings" role="tabpanel" aria-labelledby="einvoice-tab">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group d-flex align-items-center gap-3">
                                    <label class="mb-0">Enable E-Invoice</label>
                                    <div class="form-check form-switch custom-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="enable_einvoice"
                                            name="enable_einvoice" value="1">
                                        <label class="form-check-label" for="enable_einvoice">Generate IRN & QR Code on
                                            invoices</label>
                                    </div>
                                </div>
                            </div>

                            <div id="einvoice_fields_container" class="row m-0 p-0 w-100" style="display: none;">

                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>E-Invoice API Username</label>
                                        <input type="text" id="einvoice_api_username" class="form-control"
                                            placeholder="Login for einvoice1.gst.gov.in">
                                        <small class="text-muted">Login for einvoice1.gst.gov.in</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>E-Invoice API Password</label>
                                        <input type="password" id="einvoice_api_password" class="form-control"
                                            placeholder="Password for E-Invoice portal">
                                        <small class="text-muted">Password for E-Invoice portal</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Client ID</label>
                                        <input type="text" id="einvoice_client_id" class="form-control"
                                            placeholder="Header client_id issued by NIC/GSP">
                                        <small class="text-muted">Header client_id issued by NIC/GSP (NOT the random
                                            32-byte AppKey)</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Client Secret</label>
                                        <input type="text" id="einvoice_client_secret" class="form-control"
                                            placeholder="Required header client_secret">
                                        <small class="text-muted">Required header client_secret (not the GSP name)</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>E-Invoice API Endpoint URL (Optional)</label>
                                        <input type="text" id="einvoice_api_endpoint_url" class="form-control"
                                            placeholder="https://einv2api.gstsandbox.nic.in/">
                                        <small class="text-muted">Optional — E-Invoice API base URL</small>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label>E-Invoice GSTIN</label>
                                        <input type="text" id="einvoice_gstin" class="form-control"
                                            placeholder="Required for IRP authentication">
                                        <small class="text-muted">Required for IRP authentication (header gstin)</small>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-12">
                                    <div class="form-group">
                                        <label>NIC Public Key</label>
                                        <textarea id="einvoice_nic_public_key" rows="6" class="form-control"
                                            placeholder="-----BEGIN PUBLIC KEY-----..."></textarea>
                                        <small class="text-muted">Required for NIC sandbox/production: encrypts the Data
                                            field for eivital/v1.04/auth.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-3 d-flex gap-3">
                                <a href="javascript:void(0);" class="btn btn-submit" id="saveEinvoiceSettings">Save
                                    E-Invoice Settings</a>
                                <a href="javascript:void(0);" class="btn btn-cancel" id="testEinvoiceAuth">Test Auth /
                                    Public Key</a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= SALES SETTING TAB ================= -->
                    <div class="tab-pane fade" id="sales-settings" role="tabpanel" aria-labelledby="sales-tab">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <p class="text-muted small">Control which features and fields are visible on the Add Sale
                                    page.</p>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Advance Receipt</label>
                                    <select id="advance_receipt" name="advance_receipt" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0" selected>Off</option>
                                    </select>
                                    <small class="text-muted">Show Advance Receipt option on Add Sale.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Rental</label>
                                    <select id="rental" name="rental" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0" selected>Off</option>
                                    </select>
                                    <small class="text-muted">Show Rental option on Add Sale.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Financial Year</label>
                                    <select id="financial_year" name="financial_year" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                    <small class="text-muted">Enable financial year filter on sales list.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>TDS Apply</label>
                                    <select id="tds_apply" name="tds_apply" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                    <small class="text-muted">Show TDS Apply field on Add Sale.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Assign Staff</label>
                                    <select id="show_assign_staff" name="show_assign_staff" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                    <small class="text-muted">Show "Assign Staff" field on Add Sale page.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Order Type</label>
                                    <select id="show_order_type" name="show_order_type" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                    <small class="text-muted">Show "Order Type" field on Add Sale page.</small>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Payment Methods</label>

                                    <select id="payment_methods" name="payment_methods[]" class="form-control select2"
                                        multiple>
                                        <option value="cash">Cash</option>
                                        <option value="debit card">Debit Card</option>
                                        <option value="cash+online">Cash + Online</option>
                                        <option value="scan">Scan</option>
                                        <option value="emi">EMI</option>
                                    </select>

                                    <small class="text-muted">
                                        Select payment methods to display on the Sale page.
                                    </small>

                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveSalesSettings">
                                    Save Sales Settings
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ================= BARCODE SETTINGS TAB ================= -->
                    <div class="tab-pane fade" id="barcode-settings" role="tabpanel" aria-labelledby="barcode-tab">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Barcode Type</label>
                                    <select id="barcode_type" name="barcode_type" class="form-select">
                                        <option value="barcode">Barcode</option>
                                        <option value="qrcode">QR Code</option>
                                    </select>
                                    <small class="text-muted">Choose between barcode or QR code format.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label>Barcode Setting Type</label>
                                    <select id="barcode_setting_type" name="barcode_setting_type" class="form-select">
                                        <option value="simple">Simple</option>
                                        <option value="detailed">Detailed</option>
                                    </select>
                                    <small class="text-muted">Choose simple barcode or detailed barcode output.</small>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6 col-12 d-none" id="barcodeDetailWrapper">
                                <div class="form-group">
                                    <label>Detailed Barcode Format</label>
                                    <select id="barcode_detail_option" name="barcode_detail_option" class="form-select">
                                        <option value="long">Long</option>
                                        <option value="short">Short</option>
                                    </select>
                                    <small class="text-muted">Shown only when Barcode Setting Type is Detailed.</small>
                                </div>
                            </div>

                            <div class="col-lg-12 mt-4">
                                <a href="javascript:void(0);" class="btn btn-submit me-2" id="saveBarcodeSettings">
                                    Save Barcode Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            function initDashboardMobileSubsectionTabs() {
                const $dashboardRow = $("#dashboard-settings > .row");
                if (!$dashboardRow.length) return;

                const findHeadingPanel = (title) => $dashboardRow.children(".col-lg-12").filter(function() {
                    return $(this).find("h5").first().text().trim() === title;
                }).first();
                $('#payment_methods').select2({
                    placeholder: "Select Payment Methods",
                    closeOnSelect: false,
                    width: '100%'
                });
                const sections = [{
                        key: "crm",
                        heading: findHeadingPanel("CRM Dashboard Subsections")
                    },
                    {
                        key: "hr",
                        heading: findHeadingPanel("HR Dashboard Subsections")
                    },
                    {
                        key: "erp",
                        heading: findHeadingPanel("ERP Dashboard Subsections")
                    },
                ];
                const $savePanel = $("#saveDashboardSettings").closest(".col-lg-12");

                sections.forEach((section, index) => {
                    if (!section.heading.length) return;
                    const $nextBoundary = sections[index + 1]?.heading?.length ? sections[index + 1]
                        .heading : $savePanel;
                    section.items = section.heading.add(section.heading.nextUntil($nextBoundary));
                    section.items.addClass(`dashboard-mobile-panel dashboard-mobile-panel-${section.key}`);
                });

                function activateDashboardSubsection(key) {
                    $(".dashboard-mobile-subsection-tab").removeClass("active");
                    $(`.dashboard-mobile-subsection-tab[data-dashboard-subsection="${key}"]`).addClass("active");
                    $(".dashboard-mobile-panel").removeClass("active");
                    $(`.dashboard-mobile-panel-${key}`).addClass("active");
                }

                activateDashboardSubsection("crm");

                $(document).on("click", ".dashboard-mobile-subsection-tab", function() {
                    activateDashboardSubsection($(this).data("dashboard-subsection"));
                });
            }

            initDashboardMobileSubsectionTabs();

            // Multi-step Form Logic for Mobile/Tablet
            let currentStep = 1;
            const totalSteps = 4;

            function updateSteps() {
                // Update Step Buttons
                $('.step-btn').removeClass('active');
                $(`.step-btn[data-step="${currentStep}"]`).addClass('active');

                // Update Step Content
                $('.step-content').removeClass('active');
                $(`.step-${currentStep}`).addClass('active');

                // Update Navigation Buttons
                if (currentStep === 1) {
                    $('.btn-prev').addClass('d-none');
                    $('.btn-next').removeClass('d-none');
                    $('.btn-submit-mobile').addClass('d-none');
                } else if (currentStep === totalSteps) {
                    $('.btn-prev').removeClass('d-none');
                    $('.btn-next').addClass('d-none');
                    $('.btn-submit-mobile').removeClass('d-none');
                } else {
                    $('.btn-prev').removeClass('d-none');
                    $('.btn-next').removeClass('d-none');
                    $('.btn-submit-mobile').addClass('d-none');
                }

                // Scroll to top of the tab content on step change for better UX
                if ($(window).width() <= 991) {
                    $('html, body').animate({
                        scrollTop: $("#settingsTabsContent").offset().top - 100
                    }, 300);
                }
            }

            // Validation rules for each step
            const stepValidations = {
                1: [{
                        id: 'shop_name',
                        name: 'Shop Name'
                    },
                    {
                        id: 'email',
                        name: 'Shop Email'
                    },
                    {
                        id: 'phone',
                        name: 'Shop Phone'
                    },
                    {
                        id: 'address',
                        name: 'Shop Address'
                    }
                ],
                2: [{
                        id: 'bank_name',
                        name: 'Bank Name'
                    },
                    {
                        id: 'branch',
                        name: 'Branch'
                    },
                    {
                        id: 'ac_no',
                        name: 'A/C No.'
                    },
                    {
                        id: 'ifsc_code',
                        name: 'IFSC Code'
                    }
                ],
                3: [{
                        id: 'currency_symbol',
                        name: 'Currency Symbol'
                    },
                    {
                        id: 'currency_position',
                        name: 'Currency Position'
                    }
                ],
                4: [] // No required fields for step 4
            };

            // Function to validate current step
            function validateCurrentStep() {
                // Clear previous errors
                $('.step-content.active .text-danger').remove();

                const rules = stepValidations[currentStep];
                let hasError = false;

                rules.forEach(field => {
                    const value = $("#" + field.id).val();
                    if (!value || value.trim() === '') {
                        $("#" + field.id)
                            .after(
                                `<div class="text-danger mt-1"><i class="fas fa-exclamation-circle"></i> ${field.name} is required</div>`
                            );
                        hasError = true;
                    }
                });

                // if (hasError) {
                //     // Show error message
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Missing Required Fields',
                //         text: 'Please fill all required fields before proceeding.',
                //         confirmButtonText: 'OK',
                //         confirmButtonColor: '#ff9f43'
                //     });
                // }

                return !hasError;
            }

            $('.btn-next').on('click', function() {
                if (currentStep < totalSteps) {
                    if (validateCurrentStep()) {
                        currentStep++;
                        updateSteps();
                    }
                }
            });

            $('.btn-prev').on('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    updateSteps();
                }
            });

            $('.step-btn').on('click', function() {
                const targetStep = parseInt($(this).data('step'));
                // Only allow jumping backward or to same step
                // For forward navigation, validate current step first
                if (targetStep < currentStep || targetStep === currentStep) {
                    currentStep = targetStep;
                    updateSteps();
                } else if (targetStep > currentStep) {
                    // Forward navigation - validate current step
                    if (validateCurrentStep()) {
                        currentStep = targetStep;
                        updateSteps();
                    }
                }
            });

            // Handle mobile submit button click (trigger desktop submit)
            $('.btn-submit-mobile').on('click', function() {
                $("#btn-setting-submit").click();
            });

            var authToken = localStorage.getItem("authToken");
            const ImagePath = "{{ env('ImagePath') }}";
            const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
            const $generalSettingsBtn = $("#btn-setting-submit");
            const $addressField = $("#address");
            const $officeLatitudeField = $("#office_latitude");
            const $officeLongitudeField = $("#office_longitude");
            const $coordinateLookupStatus = $("#coordinateLookupStatus");
            const generalSettingsBtnDefaultHtml = $generalSettingsBtn.html();
            let lastGeocodedAddress = "";
            let geocodeDebounceTimer = null;

            function toggleGeneralSettingsBtnLoading(isLoading) {
                if (isLoading) {
                    $generalSettingsBtn
                        .addClass("disabled")
                        .attr("aria-disabled", "true")
                        .html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                        );
                    $('.btn-submit-mobile')
                        .addClass("disabled")
                        .attr("aria-disabled", "true")
                        .html(
                            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                        );
                } else {
                    $generalSettingsBtn
                        .removeClass("disabled")
                        .removeAttr("aria-disabled")
                        .html(generalSettingsBtnDefaultHtml);
                    $('.btn-submit-mobile')
                        .removeClass("disabled")
                        .removeAttr("aria-disabled")
                        .html('Submit');
                }
            }

            let url = "{{ route('general-settings.show') }}";
            if (selectedSubAdminId) {
                url += "?selectedSubAdminId=" + selectedSubAdminId;
            }

            function loadGeneralSettings() {
                $.ajax({
                    url: url,
                    type: "GET",
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        const settings = response.settings;

                        // 🏢 Company Rules
                        $("#working_hours").val(settings.working_hours ?? '');
                        $("#grace_period").val(settings.grace_period ?? '');
                        $("#lunch_break").val(settings.lunch_break ?? '');
                        $("#open_time").val(settings.open_time ? String(settings.open_time).substring(0,
                            5) : '');
                        $("#close_time").val(settings.close_time ? String(settings.close_time)
                            .substring(0, 5) : '');
                        $("#overtime_after_hours").val(settings.overtime_after_hours || '');
                        $("#tax_deduction_amount").val(settings.tax_deduction_amount ?? '0');
                        $("#salary_exceeds_amount").val(settings.salary_exceeds_amount ?? '14000');
                        $("#salary_exceeds_text").text(settings.salary_exceeds_amount ?? '14000');

                        // Location Check Enabled
                        if (settings.location_check_enabled) {
                            $("#location_check_on").prop("checked", true);
                        } else {
                            $("#location_check_off").prop("checked", true);
                        }

                        // Sunday Off
                        if (settings.sunday_off === "yes") {
                            $("#sunday_yes").prop("checked", true);
                        } else {
                            $("#sunday_no").prop("checked", true);
                        }

                        // Saturday Off
                        if (settings.saturday_off === "yes") {
                            $("#saturday_yes").prop("checked", true);
                        } else {
                            $("#saturday_no").prop("checked", true);
                        }

                        // 🏦 General Info (already working)
                        $("#low_stock").val(settings.low_stock);
                        $("#shop_name").val(settings.name);
                        $("#gst_num").val(settings.gst_num);
                        $("#cin_no").val(settings.cin_no);
                        $("#email").val(settings.email);
                        $("#phone").val(settings.phone);
                        $("#state_code").val(settings.state_code);
                        $addressField.val(settings.address);
                        $officeLatitudeField.val(settings.office_latitude ?? '');
                        $officeLongitudeField.val(settings.office_longitude ?? '');
                        $("#office_radius").val(settings.office_radius ?? 200);
                        lastGeocodedAddress = (settings.address || '').trim();
                        $("#bank_name").val(settings.bank_name);
                        $("#branch").val(settings.branch);
                        $("#ac_no").val(settings.ac_no);
                        $("#ifsc_code").val(settings.ifsc_code);
                        $("#invoice_size").val(settings.invoice_size || 'big');
                        $("#send_mail").val(
                            settings.send_mail === null || settings.send_mail === undefined ?
                            '1' :
                            String(Number(settings.send_mail))
                        );
                        $("#enable_variant_product").val(
                            settings.enable_variant_product === null || settings.enable_variant_product === undefined ?
                            '1' :
                            String(Number(settings.enable_variant_product))
                        );
                        $("#financial_year").val(
                            settings.financial_year === null || settings.financial_year ===
                            undefined ?
                            '1' :
                            String(Number(settings.financial_year))
                        );
                        $("#tds_apply").val(
                            settings.tds_apply === null || settings.tds_apply === undefined ?
                            '1' :
                            String(Number(settings.tds_apply))
                        );
                        $("#show_crm_dashboard").val(
                            settings.show_crm_dashboard === null || settings.show_crm_dashboard ===
                            undefined ?
                            '1' :
                            String(Number(settings.show_crm_dashboard))
                        );
                        $("#show_hr_dashboard").val(
                            settings.show_hr_dashboard === null || settings.show_hr_dashboard ===
                            undefined ?
                            '1' :
                            String(Number(settings.show_hr_dashboard))
                        );
                        $("#show_erp_dashboard").val(
                            settings.show_erp_dashboard === null || settings.show_erp_dashboard ===
                            undefined ?
                            '1' :
                            String(Number(settings.show_erp_dashboard))
                        );
                        // E-Way Bill Settings
                        $("#enable_eway_bill").prop("checked", settings.enable_eway_bill == 1).trigger(
                            'change');
                        $("#eway_gstin_number").val(settings.eway_gstin_number || '');
                        $("#eway_legal_business_name").val(settings.eway_legal_business_name || '');
                        $("#eway_pan_number").val(settings.eway_pan_number || '');
                        $("#eway_state").val(settings.eway_state || '');
                        $("#eway_state_code").val(settings.eway_state_code || '');
                        $("#eway_address_line_1").val(settings.eway_address_line_1 || '');
                        $("#eway_address_line_2").val(settings.eway_address_line_2 || '');
                        $("#eway_city").val(settings.eway_city || '');
                        $("#eway_pin_code").val(settings.eway_pin_code || '');
                        $("#eway_api_username").val(settings.eway_api_username || '');
                        if (settings.eway_api_password) $("#eway_api_password").val(settings
                            .eway_api_password);
                        $("#eway_app_key_client_id").val(settings.eway_app_key_client_id || '');

                        // E-Invoice Settings
                        $("#enable_einvoice").prop("checked", settings.enable_einvoice == 1).trigger(
                            'change');
                        $("#einvoice_api_username").val(settings.einvoice_api_username || '');
                        if (settings.einvoice_api_password) $("#einvoice_api_password").val(settings
                            .einvoice_api_password);
                        $("#einvoice_client_id").val(settings.einvoice_client_id || '');
                        $("#einvoice_client_secret").val(settings.einvoice_client_secret || '');
                        $("#einvoice_api_endpoint_url").val(settings.einvoice_api_endpoint_url || '');
                        $("#einvoice_gstin").val(settings.einvoice_gstin || '');
                        $("#einvoice_nic_public_key").val(settings.einvoice_nic_public_key || '');

                        // CRM Subsection Settings
                        $("#show_crm_lead_pipeline").val(
                            settings.show_crm_lead_pipeline === null || settings
                            .show_crm_lead_pipeline === undefined ?
                            '1' :
                            String(Number(settings.show_crm_lead_pipeline))
                        );
                        $("#show_crm_conversion").val(
                            settings.show_crm_conversion === null || settings
                            .show_crm_conversion === undefined ?
                            '1' :
                            String(Number(settings.show_crm_conversion))
                        );
                        $("#show_crm_followup_load").val(
                            settings.show_crm_followup_load === null || settings
                            .show_crm_followup_load === undefined ?
                            '1' :
                            String(Number(settings.show_crm_followup_load))
                        );
                        $("#show_crm_meeting_momentum").val(
                            settings.show_crm_meeting_momentum === null || settings
                            .show_crm_meeting_momentum === undefined ?
                            '1' :
                            String(Number(settings.show_crm_meeting_momentum))
                        );
                        $("#show_crm_lead_status_mix").val(
                            settings.show_crm_lead_status_mix === null || settings
                            .show_crm_lead_status_mix === undefined ?
                            '1' :
                            String(Number(settings.show_crm_lead_status_mix))
                        );
                        $("#show_crm_activity_trend").val(
                            settings.show_crm_activity_trend === null || settings
                            .show_crm_activity_trend === undefined ?
                            '1' :
                            String(Number(settings.show_crm_activity_trend))
                        );
                        $("#show_crm_pipeline_quality").val(
                            settings.show_crm_pipeline_quality === null || settings
                            .show_crm_pipeline_quality === undefined ?
                            '1' :
                            String(Number(settings.show_crm_pipeline_quality))
                        );
                        $("#show_crm_recent_leads").val(
                            settings.show_crm_recent_leads === null || settings
                            .show_crm_recent_leads === undefined ?
                            '1' :
                            String(Number(settings.show_crm_recent_leads))
                        );
                        $("#show_crm_next_7_days").val(
                            settings.show_crm_next_7_days === null || settings
                            .show_crm_next_7_days === undefined ?
                            '1' :
                            String(Number(settings.show_crm_next_7_days))
                        );

                        // HR Subsection Settings
                        $("#show_hr_staff_strength").val(
                            settings.show_hr_staff_strength === null || settings
                            .show_hr_staff_strength === undefined ?
                            '1' :
                            String(Number(settings.show_hr_staff_strength))
                        );
                        $("#show_hr_active_staff").val(
                            settings.show_hr_active_staff === null || settings
                            .show_hr_active_staff === undefined ?
                            '1' :
                            String(Number(settings.show_hr_active_staff))
                        );
                        $("#show_hr_monthly_attendance").val(
                            settings.show_hr_monthly_attendance === null || settings
                            .show_hr_monthly_attendance === undefined ?
                            '1' :
                            String(Number(settings.show_hr_monthly_attendance))
                        );
                        $("#show_hr_personal_progress").val(
                            settings.show_hr_personal_progress === null || settings
                            .show_hr_personal_progress === undefined ?
                            '1' :
                            String(Number(settings.show_hr_personal_progress))
                        );
                        $("#show_hr_attendance_pattern").val(
                            settings.show_hr_attendance_pattern === null || settings
                            .show_hr_attendance_pattern === undefined ?
                            '1' :
                            String(Number(settings.show_hr_attendance_pattern))
                        );
                        $("#show_hr_salary_payroll_trend").val(
                            settings.show_hr_salary_payroll_trend === null || settings
                            .show_hr_salary_payroll_trend === undefined ?
                            '1' :
                            String(Number(settings.show_hr_salary_payroll_trend))
                        );
                        $("#show_hr_payroll_snapshot").val(
                            settings.show_hr_payroll_snapshot === null || settings
                            .show_hr_payroll_snapshot === undefined ?
                            '1' :
                            String(Number(settings.show_hr_payroll_snapshot))
                        );
                        $("#show_hr_attendance_watch").val(
                            settings.show_hr_attendance_watch === null || settings
                            .show_hr_attendance_watch === undefined ?
                            '1' :
                            String(Number(settings.show_hr_attendance_watch))
                        );
                        $("#show_hr_payroll_status").val(
                            settings.show_hr_payroll_status === null || settings
                            .show_hr_payroll_status === undefined ?
                            '1' :
                            String(Number(settings.show_hr_payroll_status))
                        );

                        // ERP Subsection Settings
                        $("#show_erp_total_sales").val(
                            settings.show_erp_total_sales === null || settings
                            .show_erp_total_sales === undefined ?
                            '1' :
                            String(Number(settings.show_erp_total_sales))
                        );
                        $("#show_erp_total_purchase").val(
                            settings.show_erp_total_purchase === null || settings
                            .show_erp_total_purchase === undefined ?
                            '1' :
                            String(Number(settings.show_erp_total_purchase))
                        );
                        $("#show_erp_total_expense").val(
                            settings.show_erp_total_expense === null || settings
                            .show_erp_total_expense === undefined ?
                            '1' :
                            String(Number(settings.show_erp_total_expense))
                        );
                        $("#show_erp_sales_invoice_count").val(
                            settings.show_erp_sales_invoice_count === null || settings
                            .show_erp_sales_invoice_count === undefined ?
                            '1' :
                            String(Number(settings.show_erp_sales_invoice_count))
                        );
                        $("#show_erp_purchase_invoice_count").val(
                            settings.show_erp_purchase_invoice_count === null || settings
                            .show_erp_purchase_invoice_count === undefined ?
                            '1' :
                            String(Number(settings.show_erp_purchase_invoice_count))
                        );
                        $("#show_erp_customers_count").val(
                            settings.show_erp_customers_count === null || settings
                            .show_erp_customers_count === undefined ?
                            '1' :
                            String(Number(settings.show_erp_customers_count))
                        );
                        $("#show_erp_vendors_count").val(
                            settings.show_erp_vendors_count === null || settings
                            .show_erp_vendors_count === undefined ?
                            '1' :
                            String(Number(settings.show_erp_vendors_count))
                        );
                        $("#show_erp_sales_chart").val(
                            settings.show_erp_sales_chart === null || settings
                            .show_erp_sales_chart === undefined ?
                            '1' :
                            String(Number(settings.show_erp_sales_chart))
                        );
                        $("#show_erp_purchase_chart").val(
                            settings.show_erp_purchase_chart === null || settings
                            .show_erp_purchase_chart === undefined ?
                            '1' :
                            String(Number(settings.show_erp_purchase_chart))
                        );
                        $("#show_erp_recent_sales").val(
                            settings.show_erp_recent_sales === null || settings
                            .show_erp_recent_sales === undefined ?
                            '1' :
                            String(Number(settings.show_erp_recent_sales))
                        );
                        $("#show_erp_recent_purchases").val(
                            settings.show_erp_recent_purchases === null || settings
                            .show_erp_recent_purchases === undefined ?
                            '1' :
                            String(Number(settings.show_erp_recent_purchases))
                        );
                        $("#show_erp_recent_products").val(
                            settings.show_erp_recent_products === null || settings
                            .show_erp_recent_products === undefined ?
                            '1' :
                            String(Number(settings.show_erp_recent_products))
                        );
                        $("#show_erp_products_delivery").val(
                            settings.show_erp_products_delivery === null || settings
                            .show_erp_products_delivery === undefined ?
                            '1' :
                            String(Number(settings.show_erp_products_delivery))
                        );
                        $("#show_erp_recent_rentals").val(
                            settings.show_erp_recent_rentals === null || settings
                            .show_erp_recent_rentals === undefined ?
                            '1' :
                            String(Number(settings.show_erp_recent_rentals))
                        );

                        if (settings.currency_position) {
                            $("#currency_position").val(settings.currency_position).trigger("change");
                        }
                        $("#currency_symbol").val(settings.currency_symbol);
                        $("#terms_condition_eng").val(settings.terms_condition_eng ?? '');
                        $("#terms_condition_guj").val(settings.terms_condition_guj ?? '');

                        // WhatsApp & Reminder Settings
                        $("#customer_whatsapp_message").val(
                            settings.customer_whatsapp_message === null || settings
                            .customer_whatsapp_message === undefined ?
                            '1' :
                            String(Number(settings.customer_whatsapp_message))
                        );
                        $("#admin_whatsapp_message").val(
                            settings.admin_whatsapp_message === null || settings
                            .admin_whatsapp_message === undefined ?
                            '1' :
                            String(Number(settings.admin_whatsapp_message))
                        );
                        $("#advance_receipt").val(
                            settings.advance_receipt === null || settings.advance_receipt ===
                            undefined ?
                            '0' :
                            String(Number(settings.advance_receipt))
                        );
                        $("#rental").val(
                            settings.rental === null || settings.rental === undefined ?
                            '0' :
                            String(Number(settings.rental))
                        );
                        // Sales Settings new fields
                        $("#show_assign_staff").val(
                            settings.show_assign_staff === null || settings.show_assign_staff ===
                            undefined ?
                            '1' :
                            String(Number(settings.show_assign_staff))
                        );
                        $("#show_order_type").val(
                            settings.show_order_type === null || settings.show_order_type ===
                            undefined ?
                            '1' :
                            String(Number(settings.show_order_type))
                        );
                        $("#barcode_type").val(
                            settings.barcode_type === null || settings.barcode_type ===
                            undefined || settings.barcode_type === '' ?
                            'barcode' :
                            settings.barcode_type
                        );
                        $("#barcode_setting_type").val(
                            settings.barcode_setting_type === null || settings.barcode_setting_type ===
                            undefined || settings.barcode_setting_type === '' ?
                            'simple' :
                            settings.barcode_setting_type
                        );
                        $("#barcode_detail_option").val(
                            settings.barcode_detail_option === null || settings.barcode_detail_option ===
                            undefined || settings.barcode_detail_option === '' ?
                            'long' :
                            settings.barcode_detail_option
                        );
                        toggleBarcodeDetailOption();
                        
                        // Load Payment Methods
                        if (settings.payment_methods && Array.isArray(settings.payment_methods)) {
                            $("#payment_methods").val(settings.payment_methods).trigger('change');
                        }
                        
                        $("#appointment_reminder_hours_before").val(settings
                            .appointment_reminder_hours_before ?? 3);

                        // Logos and Images
                        if (settings.logo) {
                            $("#logo_preview").attr("src", ImagePath + '/storage/' + settings.logo)
                                .show();
                        }
                        if (settings.favicon) {
                            $("#favicon_preview").attr("src", ImagePath + '/storage/' + settings
                                .favicon).show();
                        }
                        if (settings.qr_code) {
                            $("#qr_preview").attr("src", ImagePath + '/storage/' + settings.qr_code)
                                .show();
                        }

                        // E-Way Bill Settings
                        $("#enable_eway_bill").prop('checked', settings.enable_eway_bill == 1);
                        $("#eway_gstin_number").val(settings.eway_gstin_number ?? '');
                        $("#eway_legal_business_name").val(settings.eway_legal_business_name ?? '');
                        $("#eway_pan_number").val(settings.eway_pan_number ?? '');
                        $("#eway_state").val(settings.eway_state ?? '');
                        $("#eway_state_code").val(settings.eway_state_code ?? '');
                        $("#eway_address_line_1").val(settings.eway_address_line_1 ?? '');
                        $("#eway_address_line_2").val(settings.eway_address_line_2 ?? '');
                        $("#eway_city").val(settings.eway_city ?? '');
                        $("#eway_pin_code").val(settings.eway_pin_code ?? '');
                        $("#eway_api_username").val(settings.eway_api_username ?? '');
                        $("#eway_api_password").val(settings.eway_api_password ?? '');
                        $("#eway_app_key_client_id").val(settings.eway_app_key_client_id ?? '');

                        // E-Invoice Settings
                        $("#enable_einvoice").prop('checked', settings.enable_einvoice == 1);
                        $("#einvoice_api_username").val(settings.einvoice_api_username ?? '');
                        $("#einvoice_api_password").val(settings.einvoice_api_password ?? '');
                        $("#einvoice_client_id").val(settings.einvoice_client_id ?? '');
                        $("#einvoice_client_secret").val(settings.einvoice_client_secret ?? '');
                        $("#einvoice_api_endpoint_url").val(settings.einvoice_api_endpoint_url ?? '');
                        $("#einvoice_gstin").val(settings.einvoice_gstin ?? '');
                        $("#einvoice_nic_public_key").val(settings.einvoice_nic_public_key ?? '');

                        syncDashboardSectionState();
                    }
                });
            }


            loadGeneralSettings(); // Load on page load

            const dashboardMainSectionMap = {
                crm: "#show_crm_dashboard",
                hr: "#show_hr_dashboard",
                erp: "#show_erp_dashboard"
            };
            const dashboardSubsectionMap = {
                crm: "#show_crm_lead_pipeline, #show_crm_conversion, #show_crm_followup_load, #show_crm_meeting_momentum, #show_crm_lead_status_mix, #show_crm_activity_trend, #show_crm_pipeline_quality, #show_crm_recent_leads, #show_crm_next_7_days",
                hr: "#show_hr_staff_strength, #show_hr_active_staff, #show_hr_monthly_attendance, #show_hr_personal_progress, #show_hr_attendance_pattern, #show_hr_salary_payroll_trend, #show_hr_payroll_snapshot, #show_hr_attendance_watch, #show_hr_payroll_status",
                erp: "#show_erp_total_sales, #show_erp_total_purchase, #show_erp_total_expense, #show_erp_sales_invoice_count, #show_erp_purchase_invoice_count, #show_erp_customers_count, #show_erp_vendors_count, #show_erp_sales_chart, #show_erp_purchase_chart, #show_erp_recent_sales, #show_erp_recent_purchases, #show_erp_recent_products, #show_erp_products_delivery, #show_erp_recent_rentals"
            };

            function syncDashboardSectionState() {
                Object.keys(dashboardMainSectionMap).forEach(function(section) {
                    const isEnabled = $(dashboardMainSectionMap[section]).val() === "1";
                    $(dashboardSubsectionMap[section]).prop("disabled", !isEnabled);
                });
            }

            function toggleBarcodeDetailOption() {
                const isDetailed = $("#barcode_setting_type").val() === "detailed";
                $("#barcodeDetailWrapper").toggleClass("d-none", !isDetailed);
                $("#barcode_detail_option").prop("disabled", !isDetailed);
            }

            function applyMainSectionState(sectionKey) {
                const isEnabled = $(dashboardMainSectionMap[sectionKey]).val() === "1";
                $(dashboardSubsectionMap[sectionKey]).val(isEnabled ? "1" : "0");
                syncDashboardSectionState();
            }

            $("#show_crm_dashboard, #show_hr_dashboard, #show_erp_dashboard").on("change", function() {
                const id = $(this).attr("id");
                const sectionKey = id === "show_crm_dashboard" ? "crm" : (id === "show_hr_dashboard" ?
                    "hr" : "erp");
                applyMainSectionState(sectionKey);
            });

            $("#barcode_setting_type").on("change", function() {
                toggleBarcodeDetailOption();
            });

            // =====================================================================
            // GEOCODING ENGINE  (Nominatim + Photon — free, no API key)
            // =====================================================================

            function setCoordinateStatus(message, type) {
                type = type || 'muted';
                $coordinateLookupStatus
                    .removeClass('text-muted text-success text-danger text-warning')
                    .addClass('text-' + type)
                    .text(message || '');
            }

            function fillCoordinateFields(coordinates) {
                $officeLatitudeField.val(coordinates && coordinates.latitude != null ? coordinates.latitude : '');
                $officeLongitudeField.val(coordinates && coordinates.longitude != null ? coordinates.longitude :
                    '');
            }

            // Scoring helper — how many address keywords appear in Nominatim display_name
            function scoreResult(result, keywords) {
                var name = (result.display_name || '').toLowerCase();
                var score = 0;
                keywords.forEach(function(kw) {
                    if (kw.length > 2 && name.indexOf(kw.toLowerCase()) !== -1) score++;
                });
                var addr = result.address || {};
                var pc = addr.postcode || '';
                keywords.forEach(function(kw) {
                    if (/^\d{6}$/.test(kw) && pc === kw) score += 10;
                });
                return score;
            }

            function extractPincode(address) {
                var m = address.match(/\b(\d{6})\b/);
                return m ? m[1] : '';
            }

            function extractKeywords(address) {
                return address.split(',').map(function(p) {
                    return p.trim();
                }).filter(Boolean);
            }

            function extractBuildingName(address) {
                var SKIP = [
                    /^(shop|office|flat|unit|room|plot|door|house|floor|gf|ff)\b/i,
                    /^\d+(st|nd|rd|th)?\s*(floor|fl)?\.?$/i,
                    /^[a-z]?\d+$/i,
                    /^\d+[-–\/]\d+$/i
                ];
                var parts = address.split(',').map(function(p) {
                    return p.trim();
                }).filter(Boolean);
                for (var i = 0; i < parts.length; i++) {
                    var p = parts[i].replace(/^\d+[,\s]*/, '').trim();
                    var skip = false;
                    for (var s = 0; s < SKIP.length; s++) {
                        if (SKIP[s].test(parts[i]) || SKIP[s].test(p)) {
                            skip = true;
                            break;
                        }
                    }
                    if (!skip && p.length > 3 && !/^\d/.test(p)) return p;
                }
                return '';
            }

            function extractCity(address) {
                var parts = address.split(',').map(function(p) {
                    return p.trim().replace(/[-–]\s*\d{6}/, '').trim();
                }).filter(Boolean);
                var states = ['gujarat', 'maharashtra', 'rajasthan', 'karnataka', 'tamil nadu', 'andhra pradesh',
                    'telangana', 'uttar pradesh', 'madhya pradesh', 'west bengal', 'kerala', 'punjab',
                    'haryana',
                    'bihar', 'odisha', 'goa', 'delhi', 'assam', 'himachal pradesh', 'uttarakhand'
                ];
                for (var i = parts.length - 1; i >= 0; i--) {
                    var lower = parts[i].toLowerCase();
                    if (/^\d{6}$/.test(parts[i])) continue;
                    if (states.indexOf(lower) !== -1) continue;
                    if (/^india$/i.test(parts[i])) continue;
                    if (parts[i].length > 2) return parts[i];
                }
                return '';
            }

            function nominatimFetch(q, keywords) {
                var url = 'https://nominatim.openstreetmap.org/search?' +
                    'q=' + encodeURIComponent(q) +
                    '&format=json&addressdetails=1&countrycodes=in&limit=10&accept-language=en';
                return fetch(url, {
                        headers: {
                            'User-Agent': 'inventory-billing/1.0'
                        }
                    })
                    .then(function(r) {
                        return r.ok ? r.json() : [];
                    })
                    .then(function(data) {
                        if (!data || !data.length) return null;
                        var sorted = data.slice().sort(function(a, b) {
                            return scoreResult(b, keywords) - scoreResult(a, keywords);
                        });
                        var best = sorted[0];
                        return {
                            lat: parseFloat(best.lat).toFixed(7),
                            lon: parseFloat(best.lon).toFixed(7),
                            name: best.display_name.substring(0, 100),
                            attemptStr: 'Nominatim'
                        };
                    })
                    .catch(function() {
                        return null;
                    });
            }

            function photonFetch(q, keywords) {
                var url = 'https://photon.komoot.io/api/?q=' + encodeURIComponent(q) +
                    '&limit=5&lang=en&bbox=68.0,8.0,97.5,37.6';
                return fetch(url, {
                        headers: {
                            'User-Agent': 'inventory-billing/1.0'
                        }
                    })
                    .then(function(r) {
                        return r.ok ? r.json() : {};
                    })
                    .then(function(data) {
                        var features = (data && data.features) ? data.features : [];
                        if (!features.length) return null;
                        var scored = features.map(function(f) {
                            var props = f.properties || {};
                            var display = [props.name, props.street, props.city, props.state, props
                                    .postcode
                                ]
                                .filter(Boolean).join(', ');
                            return {
                                feature: f,
                                display: display,
                                score: scoreResult({
                                    display_name: display,
                                    address: {
                                        postcode: props.postcode || ''
                                    }
                                }, keywords)
                            };
                        }).sort(function(a, b) {
                            return b.score - a.score;
                        });
                        var best = scored[0];
                        var coords = best.feature.geometry.coordinates; // [lon, lat]
                        return {
                            lat: parseFloat(coords[1]).toFixed(7),
                            lon: parseFloat(coords[0]).toFixed(7),
                            name: best.display.substring(0, 100),
                            attemptStr: 'Photon'
                        };
                    })
                    .catch(function() {
                        return null;
                    });
            }

            function geocodeWaterfall(address) {
                var keywords = extractKeywords(address);
                var pincode = extractPincode(address);
                var building = extractBuildingName(address);
                var city = extractCity(address);

                var cleanParts = address.split(',')
                    .map(function(p) {
                        return p.trim();
                    })
                    .filter(function(p) {
                        if (!p || p.length < 2) return false;
                        if (/^(nr\.?|near|behind|opp\.?|opposite|above|below|beside)\b/i.test(p)) return false;
                        if (/^[A-Za-z]?\/?[0-9]+/.test(p) && p.length < 8) return false;
                        return true;
                    });
                var shortQuery = cleanParts.slice(-4).join(', ');

                var p1 = pincode ? nominatimFetch(pincode + ', India', [pincode]) : Promise.resolve(null);
                var p2 = function() {
                    return shortQuery ? nominatimFetch(shortQuery, keywords) : Promise.resolve(null);
                };
                var p3 = function() {
                    return (building && city && pincode) ? nominatimFetch(building + ', ' + city + ', ' +
                        pincode + ', India', keywords) : Promise.resolve(null);
                };
                var p4 = function() {
                    return (building && city) ? nominatimFetch(building + ', ' + city + ', India', keywords) :
                        Promise.resolve(null);
                };
                var p5 = function() {
                    return nominatimFetch(address, keywords);
                };

                return p1
                    .then(function(r) {
                        return r || p2();
                    })
                    .then(function(r) {
                        return r || p3();
                    })
                    .then(function(r) {
                        return r || p4();
                    })
                    .then(function(r) {
                        return r || p5();
                    });
            }

            // "Get GPS from Address" button
            $('#btnFetchCoordinates').on('click', function() {
                var address = $addressField.val().trim();
                if (!address) {
                    setCoordinateStatus('Please enter an address first.', 'danger');
                    return;
                }
                var $btn = $(this);
                $btn.prop('disabled', true).text('Searching…');
                setCoordinateStatus('🔍 Searching…');

                var keywords = extractKeywords(address);
                var pincode = extractPincode(address);
                var building = extractBuildingName(address);
                var photonQ = building ? (building + ', ' + (extractCity(address) || 'India')) : address;

                photonFetch(photonQ, keywords)
                    .then(function(result) {
                        return result || geocodeWaterfall(address);
                    })
                    .then(function(result) {
                        if (!result) {
                            setCoordinateStatus(
                                '❌ Address not found. Try entering the PIN code only (e.g. 395009).',
                                'danger');
                            return;
                        }
                        $officeLatitudeField.val(result.lat);
                        $officeLongitudeField.val(result.lon);
                        lastGeocodedAddress = address;
                        var via = result.attemptStr ? ' [via ' + result.attemptStr + ']' : '';
                        var displayLower = (result.name || '').toLowerCase();
                        var keywordHit = keywords.some(function(kw) {
                            return kw.length > 3 && displayLower.indexOf(kw.toLowerCase()) !== -
                                1;
                        });
                        var pincodeOk = !pincode || displayLower.indexOf(pincode) !== -1;
                        if (!pincodeOk && !keywordHit) {
                            setCoordinateStatus('⚠️ Possible mismatch — verify manually: ' + result
                                .name + via, 'warning');
                        } else {
                            setCoordinateStatus('✅ Found' + via + ': ' + result.name, 'success');
                        }
                    })
                    .catch(function() {
                        setCoordinateStatus('❌ Network error. Check your internet connection.',
                            'danger');
                    })
                    .finally(function() {
                        $btn.prop('disabled', false).text('Get GPS from Address');
                    });
            });

            // Show Image Preview
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr("src", e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#logo").change(function() {
                previewImage(this, "#logo_preview");
            });

            $("#favicon").change(function() {
                previewImage(this, "#favicon_preview");
            });

            $("#qr_code").change(function() {
                previewImage(this, "#qr_preview");
            });

            // Update General Settings
            $("#btn-setting-submit").on("click", function(e) {
                e.preventDefault(); // prevent form submission if there are errors

                let lowStock = parseFloat($('#low_stock').val()) || 0;

                if (lowStock < 0) {
                    // console.log('asd');

                    $("#lowStockError").removeClass("d-none"); // show error
                    $('#low_stock').val(0); // reset to 0
                    return false;
                } else {
                    $("#lowStockError").addClass("d-none"); // hide error
                }

                $(".text-danger").remove(); // clear previous errors

                let hasError = false;
                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const fields = [{
                        id: "low_stock",
                        name: "Low Stock Warning Quantity"
                    },
                    {
                        id: "shop_name",
                        name: "Shop Name"
                    },
                    {
                        id: "email",
                        name: "Email"
                    },
                    {
                        id: "phone",
                        name: "Phone"
                    },
                    {
                        id: "address",
                        name: "Address"
                    },
                    // { id: "bank_name", name: "Bank Name" },
                    // { id: "branch", name: "Branch" },
                    // { id: "ac_no", name: "A/C No." },
                    // { id: "ifsc_code", name: "IFSC Code" },
                    {
                        id: "currency_position",
                        name: "Currency Position"
                    },
                    {
                        id: "currency_symbol",
                        name: "Currency Symbol"
                    },
                ];

                // Validate each required field
                fields.forEach(field => {
                    const value = $("#" + field.id).val();
                    if (!value) {
                        $("#" + field.id)
                            .after(`<div class="text-danger mt-1">${field.name} is required</div>`);
                        hasError = true;
                    }
                });



                // Check if logo and favicon are selected (optional: remove this if not mandatory)
                let logo = $("#logo")[0].files[0];
                let favicon = $("#favicon")[0].files[0];
                let qr_code = $("#qr_code")[0].files[0];

                if (hasError) return; // stop submission if errors found

                // Prepare FormData
                let formData = new FormData();
                formData.append("low_stock", $("#low_stock").val());
                formData.append("shop_name", $("#shop_name").val());
                formData.append("gst_num", $("#gst_num").val());
                formData.append("cin_no", $("#cin_no").val());
                formData.append("email", $("#email").val());
                formData.append("phone", $("#phone").val());
                formData.append("state_code", $("#state_code").val());
                formData.append("address", $addressField.val());
                formData.append("office_latitude", $officeLatitudeField.val());
                formData.append("office_longitude", $officeLongitudeField.val());
                formData.append("office_radius", $("#office_radius").val() || 200);
                formData.append("bank_name", $("#bank_name").val());
                formData.append("branch", $("#branch").val());
                formData.append("ac_no", $("#ac_no").val());
                formData.append("ifsc_code", $("#ifsc_code").val());
                formData.append("currency_position", $("#currency_position").val());
                formData.append("currency_symbol", $("#currency_symbol").val());
                formData.append("terms_condition_eng", $("#terms_condition_eng").val());
                formData.append("terms_condition_guj", $("#terms_condition_guj").val());
                formData.append("selectedSubAdminId", selectedSubAdminId);
                if (logo) formData.append("logo", logo);
                if (favicon) formData.append("favicon", favicon);
                if (qr_code) formData.append("qr_code", qr_code);
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("invoice_size", $("#invoice_size").val());
                formData.append("send_mail", $("#send_mail").val());
                formData.append("financial_year", $("#financial_year").val());
                formData.append("tds_apply", $("#tds_apply").val());
                formData.append("show_crm_dashboard", $("#show_crm_dashboard").val());
                formData.append("show_hr_dashboard", $("#show_hr_dashboard").val());
                formData.append("show_erp_dashboard", $("#show_erp_dashboard").val());
                formData.append("customer_whatsapp_message", $("#customer_whatsapp_message").val());
                formData.append("admin_whatsapp_message", $("#admin_whatsapp_message").val());
                formData.append("advance_receipt", $("#advance_receipt").val());
                formData.append("rental", $("#rental").val());
                formData.append("enable_variant_product", $("#enable_variant_product").val());
                formData.append("appointment_reminder_hours_before", $("#appointment_reminder_hours_before")
                    .val());

                // Send AJAX
                $.ajax({
                    url: "{{ route('general-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        toggleGeneralSettingsBtnLoading(true);
                    },
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                const field = $("#" + key);
                                if (field.length) {
                                    field
                                        .closest(".form-group")
                                        .append('<div class="text-danger mt-1">' +
                                            value[0] + "</div>");
                                }
                            });
                            Swal.fire("Validation Error",
                                "Please check the highlighted fields.", "error");
                        } else {
                            Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON
                                .message) || "Something went wrong!", "error");
                        }
                    },
                    complete: function() {
                        toggleGeneralSettingsBtnLoading(false);
                    }
                });
            });

            // ================== DELIVERY TERMS SAVE ==================
            $("#saveDeliveryTerms").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();

                const formData = new FormData();
                formData.append("terms_condition_eng", $("#terms_condition_eng").val());
                formData.append("terms_condition_guj", $("#terms_condition_guj").val());
                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('general-settings.delivery-terms') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    beforeSend: function() {
                        $button
                            .addClass("disabled")
                            .attr("aria-disabled", "true")
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                const field = $("#" + key);
                                if (field.length) {
                                    field.closest(".form-group").append(
                                        '<div class="text-danger mt-1">' + value[
                                            0] + '</div>');
                                }
                            });
                        } else {
                            Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON
                                .message) || "Something went wrong!", "error");
                        }
                    },
                    complete: function() {
                        $button
                            .removeClass("disabled")
                            .removeAttr("aria-disabled")
                            .html(defaultHtml);
                    }
                });
            });

            // ================== COMPANY RULES SAVE ==================
            $("#saveCompanyRules").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");

                // Clear previous validation errors
                $(".text-danger").remove();

                let formData = new FormData();
                formData.append("working_hours", $("#working_hours").val());
                formData.append("sunday_off", $("input[name='sunday_off']:checked").val());
                formData.append("saturday_off", $("input[name='saturday_off']:checked").val());
                formData.append("grace_period", $("#grace_period").val());
                formData.append("lunch_break", $("#lunch_break").val());
                formData.append("open_time", $("#open_time").val());
                formData.append("close_time", $("#close_time").val());
                formData.append("overtime_after_hours", $("#overtime_after_hours").val());
                formData.append("tax_deduction_amount", $("#tax_deduction_amount").val());
                formData.append("salary_exceeds_amount", $("#salary_exceeds_amount").val());
                formData.append("location_check_enabled", $("input[name='location_check_enabled']:checked")
                    .val() || 0);
                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('general-company-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Display validation messages below each input
                            $.each(errors, function(key, value) {
                                const field = $("#" + key);
                                if (field.length) {
                                    field
                                        .closest(".form-group")
                                        .append('<div class="text-danger mt-1">' +
                                            value[0] + "</div>");
                                }
                            });
                        } else {
                            Swal.fire("Error!", xhr.responseJSON.message ||
                                "Something went wrong!", "error");
                        }
                    },
                });
            });

            // ================== DASHBOARD SETTINGS SAVE ==================
            $("#saveDashboardSettings").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();

                let formData = new FormData();
                formData.append("show_crm_dashboard", $("#show_crm_dashboard").val());
                formData.append("show_hr_dashboard", $("#show_hr_dashboard").val());
                formData.append("show_erp_dashboard", $("#show_erp_dashboard").val());

                // CRM Subsection Settings
                formData.append("show_crm_lead_pipeline", $("#show_crm_lead_pipeline").val());
                formData.append("show_crm_conversion", $("#show_crm_conversion").val());
                formData.append("show_crm_followup_load", $("#show_crm_followup_load").val());
                formData.append("show_crm_meeting_momentum", $("#show_crm_meeting_momentum").val());
                formData.append("show_crm_lead_status_mix", $("#show_crm_lead_status_mix").val());
                formData.append("show_crm_activity_trend", $("#show_crm_activity_trend").val());
                formData.append("show_crm_pipeline_quality", $("#show_crm_pipeline_quality").val());
                formData.append("show_crm_recent_leads", $("#show_crm_recent_leads").val());
                formData.append("show_crm_next_7_days", $("#show_crm_next_7_days").val());

                // HR Dashboard Subsections
                formData.append("show_hr_staff_strength", $("#show_hr_staff_strength").val());
                formData.append("show_hr_active_staff", $("#show_hr_active_staff").val());
                formData.append("show_hr_monthly_attendance", $("#show_hr_monthly_attendance").val());
                formData.append("show_hr_personal_progress", $("#show_hr_personal_progress").val());
                formData.append("show_hr_attendance_pattern", $("#show_hr_attendance_pattern").val());
                formData.append("show_hr_salary_payroll_trend", $("#show_hr_salary_payroll_trend").val());
                formData.append("show_hr_payroll_snapshot", $("#show_hr_payroll_snapshot").val());
                formData.append("show_hr_attendance_watch", $("#show_hr_attendance_watch").val());
                formData.append("show_hr_payroll_status", $("#show_hr_payroll_status").val());

                // ERP Dashboard Subsections
                formData.append("show_erp_total_sales", $("#show_erp_total_sales").val());
                formData.append("show_erp_total_purchase", $("#show_erp_total_purchase").val());
                formData.append("show_erp_total_expense", $("#show_erp_total_expense").val());
                formData.append("show_erp_sales_invoice_count", $("#show_erp_sales_invoice_count").val());
                formData.append("show_erp_purchase_invoice_count", $("#show_erp_purchase_invoice_count")
                    .val());
                formData.append("show_erp_customers_count", $("#show_erp_customers_count").val());
                formData.append("show_erp_vendors_count", $("#show_erp_vendors_count").val());
                formData.append("show_erp_sales_chart", $("#show_erp_sales_chart").val());
                formData.append("show_erp_purchase_chart", $("#show_erp_purchase_chart").val());
                formData.append("show_erp_recent_sales", $("#show_erp_recent_sales").val());
                formData.append("show_erp_recent_purchases", $("#show_erp_recent_purchases").val());
                formData.append("show_erp_recent_products", $("#show_erp_recent_products").val());
                formData.append("show_erp_products_delivery", $("#show_erp_products_delivery").val());
                formData.append("show_erp_recent_rentals", $("#show_erp_recent_rentals").val());

                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('general-dashboard-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    beforeSend: function() {
                        $button
                            .addClass("disabled")
                            .attr("aria-disabled", "true")
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button
                            .removeClass("disabled")
                            .removeAttr("aria-disabled")
                            .html(defaultHtml);
                    }
                });
            });
            // ================== E-WAY BILL SETTINGS SAVE ==================
            $("#saveEwayBillSettings").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();

                let formData = new FormData();
                formData.append("enable_eway_bill", $("#enable_eway_bill").is(":checked") ? 1 : 0);
                formData.append("eway_gstin_number", $("#eway_gstin_number").val() || '');
                formData.append("eway_legal_business_name", $("#eway_legal_business_name").val() || '');
                formData.append("eway_pan_number", $("#eway_pan_number").val() || '');
                formData.append("eway_state", $("#eway_state").val() || '');
                formData.append("eway_state_code", $("#eway_state_code").val() || '');
                formData.append("eway_address_line_1", $("#eway_address_line_1").val() || '');
                formData.append("eway_address_line_2", $("#eway_address_line_2").val() || '');
                formData.append("eway_city", $("#eway_city").val() || '');
                formData.append("eway_pin_code", $("#eway_pin_code").val() || '');
                formData.append("eway_api_username", $("#eway_api_username").val() || '');
                formData.append("eway_api_password", $("#eway_api_password").val() || '');
                formData.append("eway_app_key_client_id", $("#eway_app_key_client_id").val() || '');

                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('eway-bill-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    beforeSend: function() {
                        $button.addClass("disabled").attr("aria-disabled", "true")
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(
                            defaultHtml);
                    }
                });
            });

            // ================== E-INVOICE SETTINGS SAVE ==================
            $("#saveEinvoiceSettings").on("click", function(e) {
                e.preventDefault();

                const selectedSubAdminId = localStorage.getItem("selectedSubAdminId");
                const authToken = localStorage.getItem("authToken");
                const $button = $(this);
                const defaultHtml = $button.html();

                let formData = new FormData();
                formData.append("enable_einvoice", $("#enable_einvoice").is(":checked") ? 1 : 0);
                formData.append("einvoice_api_username", $("#einvoice_api_username").val() || '');
                formData.append("einvoice_api_password", $("#einvoice_api_password").val() || '');
                formData.append("einvoice_client_id", $("#einvoice_client_id").val() || '');
                formData.append("einvoice_client_secret", $("#einvoice_client_secret").val() || '');
                formData.append("einvoice_api_endpoint_url", $("#einvoice_api_endpoint_url").val() || '');
                formData.append("einvoice_gstin", $("#einvoice_gstin").val() || '');
                formData.append("einvoice_nic_public_key", $("#einvoice_nic_public_key").val() || '');

                formData.append("selectedSubAdminId", selectedSubAdminId);
                formData.append("_token", "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('einvoice-settings.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "Authorization": "Bearer " + authToken
                    },
                    beforeSend: function() {
                        $button.addClass("disabled").attr("aria-disabled", "true")
                            .html(
                                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                            );
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(
                            defaultHtml);
                    }
                });
            });

            $("#testEinvoiceAuth").on("click", function(e) {
                e.preventDefault();
                Swal.fire("Info", "Test Authentication functionality will be implemented soon.", "info");
            });

            // Toggle E-Way Bill fields
            $("#enable_eway_bill").on("change", function() {
                if ($(this).is(":checked")) {
                    $("#eway_fields_container").slideDown();
                } else {
                    $("#eway_fields_container").slideUp();
                }
            });

            // Toggle E-Invoice fields
            $("#enable_einvoice").on("change", function() {
                if ($(this).is(":checked")) {
                    $("#einvoice_fields_container").slideDown();
                } else {
                    $("#einvoice_fields_container").slideUp();
                }
            });

            // Initialize on load
            if ($("#enable_eway_bill").is(":checked")) {
                $("#eway_fields_container").show();
            } else {
                $("#eway_fields_container").hide();
            }

            if ($("#enable_einvoice").is(":checked")) {
                $("#einvoice_fields_container").show();
            } else {
                $("#einvoice_fields_container").hide();
            }

            // Save Sales Settings
            $("#saveSalesSettings").on("click", function(e) {
                e.preventDefault();
                const $button = $(this);
                const defaultHtml = $button.html();
                $button.addClass("disabled").attr("aria-disabled", "true").html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                );

                const data = {
                    advance_receipt: $("#advance_receipt").val(),
                    rental: $("#rental").val(),
                    financial_year: $("#financial_year").val(),
                    tds_apply: $("#tds_apply").val(),
                    show_assign_staff: $("#show_assign_staff").val(),
                    show_order_type: $("#show_order_type").val(),
                    payment_methods: $("#payment_methods").val() || [],
                };

                if (selectedSubAdminId) {
                    data.selectedSubAdminId = selectedSubAdminId;
                }

                $.ajax({
                    url: "{{ route('sales-settings.update') }}",
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message ||
                                "Sales settings updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(
                            defaultHtml);
                    }
                });
            });

            $("#saveBarcodeSettings").on("click", function(e) {
                e.preventDefault();
                const $button = $(this);
                const defaultHtml = $button.html();
                $button.addClass("disabled").attr("aria-disabled", "true").html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving...'
                );

                const data = {
                    barcode_type: $("#barcode_type").val(),
                    barcode_setting_type: $("#barcode_setting_type").val(),
                };

                if (data.barcode_setting_type === "detailed") {
                    data.barcode_detail_option = $("#barcode_detail_option").val();
                }

                if (selectedSubAdminId) {
                    data.selectedSubAdminId = selectedSubAdminId;
                }

                $.ajax({
                    url: "{{ route('sales-settings.update') }}",
                    type: "POST",
                    headers: {
                        "Authorization": "Bearer " + authToken,
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: data,
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message ||
                                "Barcode settings updated successfully!",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#ff9f43",
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error!", (xhr.responseJSON && xhr.responseJSON.message) ||
                            "Something went wrong!", "error");
                    },
                    complete: function() {
                        $button.removeClass("disabled").removeAttr("aria-disabled").html(
                            defaultHtml);
                    }
                });
            });
        });
    </script>
@endpush

<?php
$file = 'resources/views/settings/generalsettings.blade.php';
$content = file_get_contents($file);

$replacement = <<<'EOD'
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
                                        <small id="coordinateLookupStatus" class="text-muted" style="line-height:1.2;"></small>
                                        <button type="button" class="btn btn-sm btn-primary ms-2 flex-shrink-0" id="btnFetchCoordinates" style="padding: 2px 8px; font-size: 11px;">Get GPS from Address</button>
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
                                    <input type="number" id="ac_no" class="form-control" placeholder="Enter A/C No.">
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
                                    <input type="number" id="low_stock" class="form-control" placeholder="Enter Low Stock Warning Quantity">
                                    <small id="lowStockError" class="text-danger d-none">Low Stock Quantity cannot be negative.</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Invoice Size</label>
                                    <select id="invoice_size" name="invoice_size" class="form-select">
                                        <option value="small">Small Size Invoice</option>
                                        <option value="big">Big Size Invoice</option>
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
                                    <label>Financial Year</label>
                                    <select id="financial_year" name="financial_year" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>TDS Apply</label>
                                    <select id="tds_apply" name="tds_apply" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Customer WhatsApp Message</label>
                                    <select id="customer_whatsapp_message" name="customer_whatsapp_message" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Admin WhatsApp Message</label>
                                    <select id="admin_whatsapp_message" name="admin_whatsapp_message" class="form-select">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Latitude</label>
                                    <input type="text" id="office_latitude" class="form-control" placeholder="e.g. 21.1268432">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Office Longitude</label>
                                    <input type="text" id="office_longitude" class="form-control" placeholder="e.g. 73.1051204">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Radius (m)</label>
                                    <input type="number" id="office_radius" class="form-control" placeholder="e.g. 200" min="0" value="200">
                                    <small class="text-muted">Staff must be within this distance from office GPS to clock in.</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Meeting / Follow-up reminder (hours before)</label>
                                    <input type="number" id="appointment_reminder_hours_before" name="appointment_reminder_hours_before" class="form-control" placeholder="e.g. 3" min="1">
                                    <small class="text-muted">IST. Default 3.</small>
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
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="logo_preview" src="" alt="Logo Preview" style="display:none; max-width: 100px; margin-top: 10px;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop Favicon</label>
                                    <div class="image-upload">
                                        <input type="file" id="favicon" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="favicon_preview" src="" alt="Favicon Preview" style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-6">
                                <div class="form-group">
                                    <label>Shop QR Code Image</label>
                                    <div class="image-upload">
                                        <input type="file" id="qr_code" accept="image/*">
                                        <div class="image-uploads">
                                            <img src="{{ env('ImagePath') . 'admin/assets/img/icons/upload.svg' }}" alt="img">
                                            <h4>Drag and drop a file to upload</h4>
                                        </div>
                                    </div>
                                    <img id="qr_preview" src="" alt="QR Preview" style="display:none; max-width: 50px; margin-top: 10px;">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-lg-12 d-flex justify-content-end gap-2">
                                <a href="javascript:void(0);" class="btn btn-cancel">Cancel</a>
                                <a href="javascript:void(0);" class="btn btn-submit" id="btn-setting-submit">Submit</a>
                            </div>
                        </div>
                    </div>
EOD;

$start_str = "                    <!-- ================= SHOP SETTINGS TAB ================= -->";
$end_str = "                    <!-- ================= COMPANY RULES TAB ================= -->";

$start_idx = strpos($content, $start_str);
$end_idx = strpos($content, $end_str);

if ($start_idx !== false && $end_idx !== false) {
    $new_content = substr($content, 0, $start_idx) . $replacement . "\n" . substr($content, $end_idx);
    file_put_contents($file, $new_content);
    echo "Replaced successfully\n";
} else {
    echo "Could not find start or end strings\n";
}

const fs = require('fs');

let content = fs.readFileSync('resources/views/rentalreturn/createrentalreturn.blade.php', 'utf8');

const replacementHtml = `
                                            <div id="paymentPanelBody">
                                                <div class="setvaluecash mt-3" id="paymentSection">
                                                    <ul>
                                                        <li>
                                                            <a href="javascript:void(0);" class="paymentmethod active">
                                                                <input type="radio" name="payment_method" value="cash" checked hidden>
                                                                <i class="fas fa-money-bill-wave"
                                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                                Cash
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="paymentmethod">
                                                                <input type="radio" name="payment_method" value="debit card" hidden>
                                                                <i class="fas fa-credit-card"
                                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                                Debit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="paymentmethod">
                                                                <input type="radio" name="payment_method" value="cash+online" hidden>
                                                                <i class="fas fa-money-check-alt"
                                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                                Cash+Online
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0);" class="paymentmethod">
                                                                <input type="radio" name="payment_method" value="scan" hidden>
                                                                <i class="fas fa-qrcode"
                                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                                Scan
                                                            </a>
                                                        </li>
                                                        <li id="emiPaymentOption">
                                                            <a href="javascript:void(0);" class="paymentmethod">
                                                                <input type="radio" name="payment_method" value="emi" hidden>
                                                                <i class="fas fa-calendar-alt"
                                                                    style="font-size: 16px; display: block; margin-bottom: 4px;"></i>
                                                                EMI
                                                            </a>
                                                        </li>
                                                    </ul>

                                                    <div id="posPaidTypeBox" style="display:none; margin-top:10px;">
                                                        <div class="form-group mb-2" id="posPaidTypeDropdownGroup">
                                                            <label for="posPaidType">Paid Type</label>
                                                            <select id="posPaidType" class="form-control">
                                                                <option value="" selected disabled>Select Paid Type</option>
                                                                <option value="fully">Fully</option>
                                                                <option value="partially">Partially</option>
                                                            </select>
                                                            <span class="error_paidtype text-danger"></span>
                                                        </div>
                                                        <div id="posPaidAmountFields" style="display:none;">
                                                            <div class="row g-2">
                                                                <div class="col-md-6 col-6">
                                                                    <div class="form-group mb-2">
                                                                        <label for="posPaidAmount">Enter Amount</label>
                                                                        <input type="number" class="form-control" id="posPaidAmount"
                                                                            placeholder="0.00" min="0" step="0.01">
                                                                        <span class="error_paidamount text-danger"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 col-6">
                                                                    <div class="form-group">
                                                                        <label for="posPendingAmount">Pending Amount</label>
                                                                        <input type="number" class="form-control" id="posPendingAmount"
                                                                            placeholder="0.00" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="cashOnlineBox" style="display:none; margin-top:10px;">
                                                        <div class="row g-2">
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Cash Amount</label>
                                                                    <input type="number" class="form-control"
                                                                        id="posCashOnlineCashAmount" placeholder="Enter Cash Amount">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Online Amount</label>
                                                                    <input type="number" class="form-control"
                                                                        id="posCashOnlineOnlineAmount" placeholder="Enter Online Amount"
                                                                        value="0" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="bankSelectionBox" style="display:none; margin-top:10px;">
                                                        <div class="row g-2">
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <div class="bank-label-row">
                                                                        <label>Select Bank</label>
                                                                    </div>
                                                                    <select name="bank_id" id="bank_id" class="form-control">
                                                                        <option value="">Select Bank</option>
                                                                        @foreach ($banks as $bank)
                                                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                                                ({{ $bank->account_number }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <span class="error_bank text-danger"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label for="posPaymentRemark">Remark</label>
                                                                    <input type="text" class="form-control" id="posPaymentRemark"
                                                                        placeholder="Enter remark">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="emiBox" style="display:none; margin-top:10px;">
                                                        <div class="row g-2">
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Down Payment (Optional)</label>
                                                                    <input type="number" class="form-control" id="emiDownPayment"
                                                                        placeholder="₹ Amount" min="0" step="0.01">
                                                                    <small class="text-danger d-none" id="emiDownPaymentError"></small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Loan Amount</label>
                                                                    <input type="number" class="form-control" id="emiLoanAmount"
                                                                        placeholder="Auto Calculate" min="0" step="0.01"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>EMI Tenure</label>
                                                                    <select class="form-control" id="emiTenure">
                                                                        <option value="">Select Tenure</option>
                                                                        <option value="3">3 Months</option>
                                                                        <option value="6">6 Months</option>
                                                                        <option value="9">9 Months</option>
                                                                        <option value="12">12 Months</option>
                                                                        <option value="custom">Custom</option>
                                                                    </select>
                                                                    <small class="text-danger d-none" id="emiTenureError"></small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6 d-none" id="emiCustomTenureCol">
                                                                <div class="form-group">
                                                                    <label>Custom Tenure (Months)</label>
                                                                    <input type="number" class="form-control" id="emiCustomTenure"
                                                                        min="1" max="120" step="1"
                                                                        placeholder="Enter months">
                                                                    <small class="text-danger d-none" id="emiCustomTenureError"></small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Interest Rate (%) <small
                                                                            class="text-muted">Optional</small></label>
                                                                    <input type="number" class="form-control" id="emiInterestRate"
                                                                        value="0" min="0" step="0.01">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Monthly EMI</label>
                                                                    <input type="number" class="form-control" id="emiMonthlyAmount"
                                                                        placeholder="Auto Calculate" min="0" step="0.01"
                                                                        readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Aadhar Number</label>
                                                                    <input type="text" class="form-control" id="emiAadharNumber"
                                                                        placeholder="Customer Aadhar Number">
                                                                    <small class="text-danger d-none" id="emiAadharError"></small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>DO ID <small class="text-muted"></small></label>
                                                                    <input type="text" class="form-control" id="emiDoId"
                                                                        placeholder="DO ID">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>PAN Number <small class="text-muted">Optional</small></label>
                                                                    <input type="text" class="form-control" id="emiPanNumber"
                                                                        placeholder="PAN Number">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-6">
                                                                <div class="form-group">
                                                                    <label>Guarantor Name <small
                                                                            class="text-muted">Optional</small></label>
                                                                    <input type="text" class="form-control" id="emiGuarantorName"
                                                                        placeholder="Guarantor Name">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <div class="bank-label-row">
                                                                        <label class="mb-0">Select Bank <small
                                                                                class="text-muted">Optional</small></label>
                                                                    </div>
                                                                    <select class="form-control" id="emiBankId">
                                                                        <option value="">Select Bank</option>
                                                                        @foreach ($banks as $bank)
                                                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }}
                                                                                ({{ $bank->account_number }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <small class="text-danger d-none" id="emiBankError"></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
`;

// Extract everything from <div class="col-md-6"> containing "Rental Payment Method" to the end of the <div class="col-md-3"> completely
// Wait, earlier we had a structure like:
// <div class="row">
//     <div class="col-md-6"> ... Rental Payment Method ... </div>
//     <div class="col-md-3"> ... Select Online Type ... </div>
//     <div class="col-md-3"> ... Pending Amount / Enter Amount ... </div>
// </div>

const startRegex = /<div class="row">\s*<div class="col-md-6">[\s\S]*?<label>Rental Payment Method<\/label>/m;

// We will replace the whole `<div class="row">...</div>` with the new structure.
// Let's find the closing of `<div class="card-body">`
const cardBodyStart = /<div class="card bg-light">\s*<div class="card-body">\s*<div class="row">/m;
const match = content.match(cardBodyStart);

if (match) {
    const startIndex = match.index + match[0].length;
    // Find the end of this row... it's before `</div> <!-- /row -->` and then `</div> <!-- /card-body -->`
    const endIndex = content.indexOf('</div>\r\n                            </div>\r\n                        </div>', startIndex);
    
    if (endIndex !== -1) {
        const newHtml = `\n                                    <div class="col-md-12">\n                                        <div class="form-group">\n                                            <label>Rental Payment Method</label>` + replacementHtml + `\n                                        </div>\n                                    </div>\n                                `;
        
        content = content.substring(0, startIndex) + newHtml + content.substring(endIndex);
        fs.writeFileSync('resources/views/rentalreturn/createrentalreturn.blade.php', content);
        console.log("HTML replaced successfully.");
    } else {
        console.log("Could not find endIndex.");
    }
} else {
    console.log("Could not find cardBodyStart.");
}

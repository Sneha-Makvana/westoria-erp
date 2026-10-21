<?php
$content = file_get_contents('resources/views/rentalreturn/createrentalreturn.blade.php');

$startFn = "$('#btn-submit').on('click', function () {";
$endFn = "        });\n    });";
$endFn2 = "        });\r\n    });";

$startIndex = strpos($content, $startFn);
if ($startIndex !== false) {
    $endIndex = strpos($content, $endFn, $startIndex);
    if ($endIndex === false) {
        $endIndex = strpos($content, $endFn2, $startIndex);
        if ($endIndex !== false) $endIndex += strlen($endFn2);
    } else {
        $endIndex += strlen($endFn);
    }

    if ($endIndex !== false) {
        $newSubmitJs = <<<'JS'
    $('#btn-submit').on('click', function () {
        if (!originalOrderId) {
            alert('Please select an order.');
            return;
        }

        const paymentMethod = $('input[name="payment_method"]:checked').val();
        if (!paymentMethod) {
            alert('Please select a payment method.');
            return;
        }

        let enteredAmount = 0;
        let remaining = (originalTotalAmount - originalPaidAmount);
        if (remaining < 0) remaining = 0;

        const onlineType = $('#posPaidType').val();
        
        if (paymentMethod === 'cash') {
            enteredAmount = remaining;
        } else if (onlineType === 'fully') {
            enteredAmount = remaining;
        } else if (onlineType === 'partially') {
            enteredAmount = parseFloat($('#posPaidAmount').val()) || 0;
        }

        // Validate products
        let hasQuantity = false;
        const productsData = [];
        $('.quantity-input').each(function () {
            const qty = parseFloat($(this).val()) || 0;
            if (qty > 0) hasQuantity = true;
            
            const price = parseFloat($(this).data('price')) || 0;
            productsData.push({
                order_item_id: $(this).data('item-id'),
                quantity: qty,
                price: price,
                subtotal: qty * price
            });
        });

        if (!hasQuantity) {
            alert('Please enter a return quantity for at least one item.');
            return;
        }

        const formData = {
            order_id: originalOrderId,
            payment_method: paymentMethod,
            paid_type: onlineType,
            grand_total: currentTotalReturnAmount,
            entered_amount: enteredAmount,
            reviews: $('#reviews').val(),
            products: productsData,
            
            // New payment fields
            bank_id: $('#bank_id').val(),
            remark: $('#posPaymentRemark').val(),
            cash_amount: $('#posCashOnlineCashAmount').val(),
            online_amount: $('#posCashOnlineOnlineAmount').val(),
            
            // EMI Fields
            emi_down_payment: $('#emiDownPayment').val(),
            emi_loan_amount: $('#emiLoanAmount').val(),
            emi_tenure: $('#emiTenure').val(),
            emi_custom_tenure: $('#emiCustomTenure').val(),
            emi_interest_rate: $('#emiInterestRate').val(),
            emi_monthly_amount: $('#emiMonthlyAmount').val(),
            emi_aadhar_number: $('#emiAadharNumber').val(),
            emi_do_id: $('#emiDoId').val(),
            emi_pan_number: $('#emiPanNumber').val(),
            emi_guarantor_name: $('#emiGuarantorName').val(),
            emi_bank_id: $('#emiBankId').val(),
        };

        $('#btn-submit').prop('disabled', true).text('Processing...');

        $.ajax({
            url: '/api/return_rental',
            type: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    alert('Rental Return completed successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + response.message);
                    $('#btn-submit').prop('disabled', false).text('Submit Return \u2192');
                }
            },
            error: function (xhr) {
                let errorMsg = 'An error occurred while submitting the return.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
                $('#btn-submit').prop('disabled', false).text('Submit Return \u2192');
            }
        });
    });
JS;
        $content = substr($content, 0, $startIndex) . $newSubmitJs . substr($content, $endIndex);
        file_put_contents('resources/views/rentalreturn/createrentalreturn.blade.php', $content);
        echo "Submit JS updated.\n";
    }
}
?>

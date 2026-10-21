<?php
$content = file_get_contents('resources/views/rentalreturn/createrentalreturn.blade.php');

$jsToReplaceStart = "$('#payment_method').on('change', function () {";
$jsToReplaceEnd = "        $('#pending_amount_display').text(formatCurrency(pending));\n    }";
// In createrentalreturn.blade.php, it's:
//     $('#payment_method').on('change', function () {
// ...
//     function calculatePendingAmount() {
// ...
//         $('#pending_amount_display').text(formatCurrency(pending));
//     }

$startIndex = strpos($content, "$('#payment_method').on('change', function () {");
if ($startIndex !== false) {
    // Find the end of calculatePendingAmount()
    $endIndexStr = "        $('#pending_amount_display').text(formatCurrency(pending));\n    }";
    $endIndexStr2 = "        $('#pending_amount_display').text(formatCurrency(pending));\r\n    }";
    $endIndex = strpos($content, $endIndexStr, $startIndex);
    if ($endIndex === false) {
        $endIndex = strpos($content, $endIndexStr2, $startIndex);
        if ($endIndex !== false) $endIndex += strlen($endIndexStr2);
    } else {
        $endIndex += strlen($endIndexStr);
    }
    
    if ($endIndex !== false) {
        $newJs = <<<'JS'
    $('.paymentmethod').on('click', function () {
        $('.paymentmethod').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[name="payment_method"]').prop('checked', true);

        const method = $(this).find('input[name="payment_method"]').val();
        
        // Hide all
        $('#posPaidTypeBox, #cashOnlineBox, #bankSelectionBox, #emiBox').hide();
        
        if (method === 'debit card' || method === 'scan') {
            $('#posPaidTypeBox').show();
            $('#bankSelectionBox').show();
        } else if (method === 'cash+online') {
            $('#posPaidTypeBox').show();
            $('#bankSelectionBox').show();
            $('#cashOnlineBox').show();
        } else if (method === 'emi') {
            $('#emiBox').show();
        }
        
        $('#posPaidType').trigger('change');
    });

    $('#posPaidType').on('change', function () {
        if ($(this).val() === 'partially') {
            $('#posPaidAmountFields').show();
            $('#posPaidAmount').val(0);
        } else {
            $('#posPaidAmountFields').hide();
            $('#posPaidAmount').val(currentTotalReturnAmount);
        }
        calculatePendingAmount();
    });

    $('#posPaidAmount, #posCashOnlineCashAmount').on('input', function () {
        calculatePendingAmount();
    });

    function calculatePendingAmount() {
        let remaining = (originalTotalAmount - originalPaidAmount);
        if (remaining < 0) remaining = 0;
        
        let entered = parseFloat($('#posPaidAmount').val()) || 0;
        
        const method = $('input[name="payment_method"]:checked').val();
        if (method === 'cash') {
            entered = remaining;
        } else if ($('#posPaidType').val() === 'fully') {
            entered = remaining;
        }
        
        if (method === 'cash+online') {
            let cashAmt = parseFloat($('#posCashOnlineCashAmount').val()) || 0;
            let onlineAmt = entered - cashAmt;
            if (onlineAmt < 0) onlineAmt = 0;
            $('#posCashOnlineOnlineAmount').val(onlineAmt);
        }
        
        let pending = remaining - entered;
        if (pending < 0) pending = 0;
        
        $('#posPendingAmount').val(pending.toFixed(2));
        $('#pending_amount_display').text(formatCurrency(pending));
    }
JS;
        $content = substr($content, 0, $startIndex) . $newJs . substr($content, $endIndex);
        file_put_contents('resources/views/rentalreturn/createrentalreturn.blade.php', $content);
        echo "JS replaced successfully.\n";
    } else {
        echo "End index not found.\n";
    }
} else {
    echo "Start index not found.\n";
}

// Also need to update submit logic
//     const paymentMethod = $('#payment_method').val();
// to
//     const paymentMethod = $('input[name="payment_method"]:checked').val();
?>

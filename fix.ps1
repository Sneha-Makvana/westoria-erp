
$files = @("salse-invoice-small-pdf.blade.php", "salse-invoice-small-pdf-2.blade.php", "salse-invoice-small-pdf-3.blade.php", "salse-invoice-small-pdf-4.blade.php", "salse-invoice-small-pdf-5.blade.php")
foreach ($filename in $files) {
    $path = "e:\xampp8.1\htdocs\pooja-erp\resources\views\sales\$filename"
    $content = Get-Content $path -Raw

    # 1. Update calc block
    $calcFind = "\`$afterDiscount = \`$baseSubtotal - \`$totalDiscount;"
    $calcReplace = "\`$afterDiscount = \`$baseSubtotal - \`$totalDiscount;`n        \`$globalDiscountPercent = isset(\`$discountPercent) ? (float)\`$discountPercent : (float)(\`$sales->discount ?? 0);`n        \`$parsedDiscountAmount = 0;`n        if (isset(\`$discountAmount)) {`n            \`$parsedDiscountAmount = (float)str_replace(',', ', (string)\`$discountAmount);`n        } else {`n            \`$parsedDiscountAmount = (\`$baseSubtotal * \`$globalDiscountPercent) / 100;`n        }`n        \`$globalDiscountAmount = \`$parsedDiscountAmount;`n        \`$overallDiscount = \`$totalDiscount + \`$globalDiscountAmount;`n        \`$afterDiscount = \`$baseSubtotal - \`$overallDiscount;"
    $content = $content.Replace($calcFind, $calcReplace)

    # 2. Add TDS block
    $tdsFind = "<tr>`n            <td class=`"text-left bold`">Total</td>"
    if ($content.IndexOf($tdsFind) -eq -1) {
        $tdsFind = "<tr>`n                <td class=`"text-left bold`">Total</td>"
    }
    $tdsReplace = "@if(isset(\`$sales->tds_percentage) && \`$sales->tds_percentage > 0)`n        <tr>`n            <td class=`"text-left`">TDS({{ (float)\`$sales->tds_percentage }}%)</td>`n            <td class=`"text-right`">-{{ number_format((float)(\`$sales->tds_amount ?? 0), 2) }}</td>`n        </tr>`n        @endif`n        " + $tdsFind.TrimStart()
    $content = $content.Replace($tdsFind, $tdsReplace)

    # 3. Replace MRP item price
    if ($filename -ne "salse-invoice-small-pdf.blade.php") {
        $mrpFind = "<td class=`"w-15`">{{ number_format(\`$item->price, 2) }}</td>"
        $mrpReplace = "<td class=`"w-15`">{{ number_format(\`$item->product->mrp ?? \`$item->price, 2) }}</td>"
        $content = $content.Replace($mrpFind, $mrpReplace)
    }

    # 4. Remove <br>Discount(%)
    $content = $content.Replace("<br>Discount(%)", "")

    # 5. Fix CSS in template 2
    if ($filename -eq "salse-invoice-small-pdf-2.blade.php") {
        $content = $content.Replace(".w-45 { width: 45%; }", ".w-45 { width: 34%; }")
        $content = $content.Replace(".w-15 { width: 15%; text-align: center; }", ".w-15 { width: 18%; text-align: right; } /* Amount */")
        $content = $content.Replace(".w-15-price { width: 15%; text-align: center; }", ".w-15-price { width: 16%; text-align: right; padding-right: 2px; } /* Price */")
        $content = $content.Replace(".w-20 { width: 20%; text-align: right; }", ".w-20 { width: 27%; text-align: center; } /* Qty */")
    }

    # 6. Re-do the totals table
    if ($filename -ne "salse-invoice-small-pdf.blade.php") {
        $totalsRegex = [regex]::new("(?s)<table class=`"totals-table`">.*?</table")
        $newTotals = "<table class=`"totals-table`">`n        @if (\`$totalGst > 0)`n        <tr>`n            <td class=`"text-left`">Tax(GST)</td>`n            <td class=`"text-right`">{{ number_format(\`$totalGst, 2) }}</td>`n        </tr>`n        @endif`n        @if (\`$shippingCharge > 0)`n        <tr>`n            <td class=`"text-left`">Shipping</td>`n            <td class=`"text-right`">{{ number_format(\`$shippingCharge, 2) }}</td>`n        </tr>`n        @endif`n        @if (\`$otherChargesTotal > 0)`n        <tr>`n            <td class=`"text-left`">Other</td>`n            <td class=`"text-right`">{{ number_format(\`$otherChargesTotal, 2) }}</td>`n        </tr>`n        @endif`n        @if (\`$globalDiscountAmount > 0 || \`$globalDiscountPercent > 0 || \`$totalDiscount > 0)`n        <tr>`n            <td class=`"text-left`">Disc.({{ (float)\`$globalDiscountPercent }}%)</td>`n            <td class=`"text-right`">-{{ number_format(\`$globalDiscountAmount > 0 ? \`$globalDiscountAmount : \`$totalDiscount, 2) }}</td>`n        </tr>`n        @endif`n        @if (\`$overallDiscount > 0)`n        <tr>`n            <td class=`"text-left`">Total Disc.</td>`n            <td class=`"text-right`">-{{ number_format(\`$overallDiscount, 2) }}</td>`n        </tr>`n        @endif`n        @if(isset(\`$sales->tds_percentage) && \`$sales->tds_percentage > 0)`n        <tr>`n            <td class=`"text-left`">TDS({{ (float)\`$sales->tds_percentage }}%)</td>`n            <td class=`"text-right`">-{{ number_format((float)(\`$sales->tds_amount ?? 0), 2) }}</td>`n        </tr>`n        @endif`n        <tr>`n            <td class=`"text-left bold`">Total</td>`n            <td class=`"text-right bold`">{{ number_format(\`$grandTotal, 2) }}</td>`n        </tr>`n        <tr>`n            <td class=`"text-left`">Received</td>`n            <td class=`"text-right`">{{ number_format(\`$paidAmount, 2) }}</td>`n        </tr>`n        <tr>`n            <td class=`"text-left`">Balance</td>`n            <td class=`"text-right`">{{ number_format(\`$pendingAmount, 2) }}</td>`n        </tr>`n    </table"
        $content = $totalsRegex.Replace($content, $newTotals)
    }

    Set-Content $path $content
}


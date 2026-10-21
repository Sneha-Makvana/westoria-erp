<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR DASHBOARD NUMBERS
   ========================================= */
/* Top small stat cards (Sales Invoice, Purchase Invoice, etc.) */
:root[data-theme="dark"] .color_box .dash-counts h4,
:root[data-theme="dark"] .color_box .dash-counts h5,
:root[data-theme="dark"] .dash-count h4,
:root[data-theme="dark"] .dash-count h5,
:root[data-theme="dark"] .dash-widgetcontent h4,
:root[data-theme="dark"] .dash-widgetcontent h5 {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .color_box .dash-count {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

/* Latest Sales/Purchases internal boxes (Total Orders, This Month) */
:root[data-theme="dark"] .card-body .row .col-6 h4,
:root[data-theme="dark"] .card-body .row .col-6 h5,
:root[data-theme="dark"] .card-body .row .col-6 p {
    color: #f1f5f9 !important;
}

/* Internal boxes backgrounds */
:root[data-theme="dark"] .card-body .row .col-6 > a > div,
:root[data-theme="dark"] .card-body .row .col-6 > div {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added dashboard numbers dark mode overrides successfully.";
?>

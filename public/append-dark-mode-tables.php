<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR CRM TABLES & BADGES
   ========================================= */
:root[data-theme="dark"] .table thead th {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
    border-color: #475569 !important;
}

:root[data-theme="dark"] .table tbody td {
    background-color: transparent !important;
    color: #f1f5f9 !important;
    border-color: #475569 !important;
}

:root[data-theme="dark"] .crm-mini-badge,
:root[data-theme="dark"] .badge.bg-light,
:root[data-theme="dark"] .bg-light {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .card-body {
    border-color: #334155 !important;
}

:root[data-theme="dark"] .table-responsive {
    background-color: transparent !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added table dark mode overrides successfully.";
?>

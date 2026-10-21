<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR CRM & HR METRICS
   ========================================= */
:root[data-theme="dark"] .crm-metric-value,
:root[data-theme="dark"] .hr-metric-value {
    color: #ffffff !important;
}

:root[data-theme="dark"] .crm-metric-label,
:root[data-theme="dark"] .hr-metric-label {
    color: #cbd5e1 !important;
}

:root[data-theme="dark"] .crm-metric-note,
:root[data-theme="dark"] .hr-metric-note {
    color: #94a3b8 !important;
}

:root[data-theme="dark"] .crm-metric,
:root[data-theme="dark"] .hr-metric {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added CRM and HR metrics dark mode overrides successfully.";
?>

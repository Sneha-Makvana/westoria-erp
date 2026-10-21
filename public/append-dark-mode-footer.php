<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR BUTTONS & FOOTER
   ========================================= */
:root[data-theme="dark"] .btn-cancel {
    background-color: #334155 !important;
    color: #f1f5f9 !important;
    border-color: #475569 !important;
}
:root[data-theme="dark"] .btn-cancel:hover {
    background-color: #475569 !important;
}

:root[data-theme="dark"] footer {
    background-color: #0f172a !important;
    border-top: 1px solid #1e293b !important;
}
:root[data-theme="dark"] footer h1,
:root[data-theme="dark"] footer h1 a {
    color: #94a3b8 !important;
}
:root[data-theme="dark"] footer h1 a:hover {
    color: #cbd5e1 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added button and footer dark mode overrides successfully.";
?>

<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR ICONS
   ========================================= */
:root[data-theme="dark"] .fa-clock,
:root[data-theme="dark"] .notification-bell,
:root[data-theme="dark"] .header .nav-link i.fa-regular,
:root[data-theme="dark"] .header .nav-link i.fa,
:root[data-theme="dark"] .header .nav-link i.fas {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] #theme-toggle-icon.fa-sun {
    color: #ff9f43 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added icon dark mode overrides successfully.";
?>

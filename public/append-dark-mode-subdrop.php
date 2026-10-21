<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR SIDEBAR SUBMENU (.subdrop)
   ========================================= */
:root[data-theme="dark"] .sidebar-menu li a.subdrop {
    background-color: #1e293b !important;
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .sidebar-menu li a.subdrop i,
:root[data-theme="dark"] .sidebar-menu li a.subdrop span {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .sidebar-menu li.submenu > a:hover {
    color: #ff9f43 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added sidebar subdrop dark mode overrides successfully.";
?>

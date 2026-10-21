<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR SIDEBAR ACTIVE & FORM FOCUS
   ========================================= */
:root[data-theme="dark"] .sidebar-menu li.active > a,
:root[data-theme="dark"] .sidebar-menu li a.active,
:root[data-theme="dark"] .sidebar .sidebar-menu > ul > li.active > a {
    color: #ff9f43 !important;
    background-color: rgba(255, 159, 67, 0.1) !important;
}

:root[data-theme="dark"] .sidebar-menu li.active > a i,
:root[data-theme="dark"] .sidebar-menu li a.active i {
    color: #ff9f43 !important;
}

:root[data-theme="dark"] .form-control:focus,
:root[data-theme="dark"] .select2-container--default .select2-selection--single:focus,
:root[data-theme="dark"] input:focus,
:root[data-theme="dark"] textarea:focus {
    background-color: #0f172a !important;
    color: #f1f5f9 !important;
    border-color: #ff9f43 !important;
    box-shadow: 0 0 0 0.25rem rgba(255, 159, 67, 0.25) !important;
}

/* Fix for Chrome autofill background in dark mode */
:root[data-theme="dark"] input:-webkit-autofill,
:root[data-theme="dark"] input:-webkit-autofill:hover, 
:root[data-theme="dark"] input:-webkit-autofill:focus, 
:root[data-theme="dark"] input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #0f172a inset !important;
    -webkit-text-fill-color: #f1f5f9 !important;
    transition: background-color 5000s ease-in-out 0s;
}

/* Ensure all inputs have dark bg just in case */
:root[data-theme="dark"] input:not([type="submit"]):not([type="checkbox"]):not([type="radio"]),
:root[data-theme="dark"] textarea,
:root[data-theme="dark"] select {
    background-color: #0f172a !important;
    color: #f1f5f9 !important;
}

CSS;

file_put_contents($cssPath, $overrides, FILE_APPEND);
echo "Added sidebar and form dark mode overrides successfully.";
?>

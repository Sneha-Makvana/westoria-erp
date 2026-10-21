<?php
$cssPath = __DIR__ . '/admin/assets/css/style.css';

$overrides = <<<CSS

/* =========================================
   DARK MODE OVERRIDES FOR BOOTSTRAP & GLOBALS
   ========================================= */
:root[data-theme="dark"] body {
    background-color: #0f172a !important;
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .bg-white {
    background-color: #1e293b !important;
}

:root[data-theme="dark"] .card {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

:root[data-theme="dark"] .text-dark {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .text-muted {
    color: #cbd5e1 !important;
}

:root[data-theme="dark"] .table {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

:root[data-theme="dark"] .modal-content {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

:root[data-theme="dark"] .form-control,
:root[data-theme="dark"] .select2-selection {
    background-color: #0f172a !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .header {
    background-color: #1e293b !important;
    border-bottom: 1px solid #334155 !important;
}

:root[data-theme="dark"] .sidebar {
    background-color: #1e293b !important;
    border-right: 1px solid #334155 !important;
}

:root[data-theme="dark"] .sidebar-menu li a {
    color: #cbd5e1 !important;
}

:root[data-theme="dark"] .sidebar-menu li a:hover {
    color: #ff9f43 !important;
    background-color: rgba(255, 159, 67, 0.1) !important;
}

:root[data-theme="dark"] .dropdown-menu {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

:root[data-theme="dark"] .dropdown-item {
    color: #f1f5f9 !important;
}

:root[data-theme="dark"] .dropdown-item:hover {
    background-color: rgba(255, 159, 67, 0.1) !important;
    color: #ff9f43 !important;
}

:root[data-theme="dark"] .dash-widget,
:root[data-theme="dark"] .dash-count {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}

:root[data-theme="dark"] .list-group-item {
    background-color: #1e293b !important;
    border-color: #334155 !important;
}
CSS;

if (strpos(file_get_contents($cssPath), 'DARK MODE OVERRIDES') === false) {
    file_put_contents($cssPath, $overrides, FILE_APPEND);
    echo "Added dark mode overrides successfully.";
} else {
    echo "Overrides already present.";
}
?>

<?php

return [

    /*
    | Marker file created when web setup finishes successfully.
    */
    'installed_file' => storage_path('app/.installed'),

    /*
    | URI paths allowed before installation completes.
    */
    'except_paths' => [
        'setup',
        'setup/*',
        'license',
        'license/*',
        'up',
    ],

];

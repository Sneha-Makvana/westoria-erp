<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class GstWorkbookImport implements ToArray
{
    public function array(array $array)
    {
        // Laravel Excel fills the sheet arrays through Excel::toArray().
    }
}

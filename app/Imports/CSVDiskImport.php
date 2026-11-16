<?php

namespace App\Imports;

use App\Model\Disk;
use Maatwebsite\Excel\Concerns\ToModel;

class CSVDiskImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Disk([
            'description' => $row[0],
            'path' => $row[1],
            'size' => $row[2],
            'usage' => $row[3],
        ]);
    }
}

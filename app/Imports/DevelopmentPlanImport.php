<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DevelopmentPlanImport implements WithMultipleSheets 
{
    public $idpSheetImport;

    public function __construct()
    {
        $this->idpSheetImport = new IdpSheetImport();
    }

    public function sheets(): array
    {
        return [
            'IDP' => $this->idpSheetImport,
        ];
    }
}
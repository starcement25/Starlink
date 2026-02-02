<?php

namespace App\Exports;

use App\Models\Catalogue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CatalogueExport implements FromQuery, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    

    public function headings(): array
    {
        return[ 
                'Image',
                'Name',
                'Mason category',
                'Type',
                'Code',
                'Description',
                'Point',
                'Status',               
            ];
    }

    public function map($catalogue): array
    {
       
         return[
            is_file($catalogue->image) ? url("".$catalogue->image) :'',
            $catalogue->name ?? "",
            $catalogue->mason_category->name ?? "",
            $catalogue->catalogueType->name ?? "",
            $catalogue->catalogue_code ?? "",
            $catalogue->description ?? "",
            $catalogue->point ?? "",
            $catalogue->status == 1 ? "Active" : "Disabled" ?? "",
         ];
    }


    public function query()
    {

        return Catalogue::with('mason_category')->orderBy('id', 'DESC');
    }
}

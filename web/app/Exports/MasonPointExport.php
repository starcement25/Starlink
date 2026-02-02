<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MasonPointExport implements FromQuery, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return[ 
                'Name',
                'Contact',
                'Points',
                'Mason Category',
                'Branch',
                'Zone',
                'TE Code',
                'TE Name',
                'TE Mobile' ,              
                'Status' ,              
            ];
    }

    public function map($user): array
    {
       
         return[
            $user->name ?? "",
            $user->phone ?? "",
            $user->points ?? "",
            $user->mason_category->name ?? "",
            $user->branch->name ?? "",
            $user->branch->zone->name ?? "",
            $user->te_linked->emp_code ?? "",
            $user->te_linked->name ?? "",
            $user->te_linked->phone ?? "",
            $user->getUserStatus()
         ];
    }


    public function query()
    {

        return User::where('role', 2)->select();
    }
}

<?php

namespace App\Exports;

use App\Models\Lifting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LiftingExport implements FromQuery, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Lifting::all();
    }

    public function headings(): array
    {
        return[ 
                'Lifting Date',
                'Dealer Code',
                'Dealer',
                'Mason',
                'Mason Mobile',
                'Mason Branch',              
                'Product Name',              
                'Qty',              
                'Remark',               
            ];
    }

    public function map($lifting): array
    {
       
         return[
            $lifting->lifting_date ?? "",
            $lifting->user->emp_code ?? "",
            $lifting->user->name ?? "",
            $lifting->mason_user->user->name ?? "",
            $lifting->mason_user->user->phone ?? "",
            $lifting->mason_user->user->branch->name ?? "",
            $lifting->product->name ?? "",
            $lifting->qty ?? "",
            $lifting->remark ?? "",
         ];
    }


    public function query()
    {
        return Lifting::with(['product' => function($q){
            $q->select('id', 'name');
        }])->with(['user'=> function($query){
            $query->select('id', 'name','emp_code');
        }])->with(['mason_user','mason_user.user','mason_user.user.branch'])
        ->orderBy('id', 'DESC');
    }
}

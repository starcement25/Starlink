<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DealerExport implements FromQuery, WithMapping, WithHeadings
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
                'Customer Code',
                'SAP Code',
                'Name',
                'Type',
                'Linked Dealer Code',
                'Linked Dealer Name',
                'Branch',              
                'Phone',              
                'WA No',              
                'Status',               
            ];
    }

    public function map($user): array
    {
       
         return[
            $user->emp_code ?? "",
            $user->sap_code ?? "",
            $user->name ?? "",
            $user->roles->role_name ?? "",
            $user->dealer_linked->emp_code ?? "",
            $user->dealer_linked->name ?? "",
            $user->branch->name ?? "",
            $user->phone ?? "",
            $user->whatsapp_no ?? "",
            $user->status ==1 ? "Active" : "Disabled" ?? "",
         ];
    }


    public function query()
    {

        return User::with('roles')->with('branch')->with('dealer_linked')->where('status', 1)->whereIn('role', ['3', '4', '6'])->orderBy('id', 'DESC')
            ->select(["users.*"]);
    }
}

<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MasonExport implements FromQuery, WithMapping, WithHeadings
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
                'Address1',
                'Address2',
                'City',
                'District',
                'State',              
                'Country',              
                'Pincode',              
                'Aadhaar_no',              
                'Dob',              
                'Phone',     
                'Marital Status',     
                'Spouse Name',     
                'Spouse Dob',     
                'Branch', 
                'Zone', 
                'Status',    
                'Created By',
                'Linked TE',
                'Linked Dealer Code',
                'Linked Dealer Name',
                'Points',     
                'Login Status',     
                'Device Type',     
                'Device Name', 
                'App Version', 
                'Created At', 
            ];
    }

    public function map($user): array
    {
                $dealers="";
                $dealerCodes="";
                $i = 0;
                foreach($user->mason_dealers as $val)
                {
                    if($i!=0)
                    {
                        $dealers.=", ";
                        $dealerCodes.=", ";
                    }
                    $dealers.=$val->dealer->name ?? "";
                    $dealerCodes.=$val->dealer->emp_code ?? "";
                    $i++;
                }
         return[
            $user->name,
            $user->address1,
            $user->address2,
            $user->city,
            $user->district,
            $user->state,
            $user->country,
            $user->pincode,
            $user->aadhaar_no,
            $user->dob,
            $user->phone,
            $user->marital_status == '1' ? "Yes" : "",
            $user->spouse_name,
            $user->spouse_dob,
            $user->branch->name ?? "",
            $user->branch->zone->name ?? "",
            $user->status == 1 ? "Active" : "Disabled",
            $user->by_created->name ?? "",
            $user->te_linked->name ?? "",
            $dealerCodes,
            $dealers,
            $user->points,
            $user->login_status == 1 ? "Y" : "N", 
            $user->login_device_type,
            $user->login_device_name,
            $user->app_version,
            $user->created_at,
         ];
    }


    public function query()
    {

        return User::with('mason_dealers')->with('te_linked')->with('states')->with('branch')->with('by_created')->where('role', 2)->orderBy('id', 'DESC');
    }
}

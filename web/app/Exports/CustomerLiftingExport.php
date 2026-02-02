<?php

namespace App\Exports;

use App\Models\CustomerLifting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerLiftingExport implements FromQuery, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CustomerLifting::all();
    }

    public function headings(): array
    {
        return[ 
                'Code',
                'Dealer Name',
                'Dealer Code',
                'Year',
                'Branch',
                'Month',              
                'Linked Dealer',              
                'Product Name',              
                'Quantity',              
                'Status',               
            ];
    }

    public function map($customerLifting): array
    {
         return[
            $customerLifting->lifting_code ?? "",
            $customerLifting->dealer->name ?? "",
            $customerLifting->dealer->emp_code ?? "",
            $customerLifting->year ?? "",
            $customerLifting->dealer->branch->name ?? "",
            $customerLifting->month_name ?? "",
            $customerLifting->dealer->dealer_linked->name ?? "",
            $customerLifting->product->name ?? "",
            $customerLifting->quantity ?? "",
            $customerLifting->status ==1 ? "Active" : "Disabled" ?? "",
         ];
    }


    public function query()
    {

        return CustomerLifting::with(['dealer','dealer.branch', 'dealer.dealer_linked'])->with('product')->orderBy('id', 'DESC')->select([
            'customer_liftings.*',
            \DB::raw("(
                CASE WHEN customer_liftings.month = 1 THEN 'January' 
                WHEN customer_liftings.month = 2 THEN 'February'
                WHEN customer_liftings.month = 3 THEN 'March'
                WHEN customer_liftings.month = 4 THEN 'April'
                WHEN customer_liftings.month = 5 THEN 'May'
                WHEN customer_liftings.month = 6 THEN 'June'
                WHEN customer_liftings.month = 7 THEN 'July'
                WHEN customer_liftings.month = 8 THEN 'August'
                WHEN customer_liftings.month = 9 THEN 'September'
                WHEN customer_liftings.month = 10 THEN 'October'
                WHEN customer_liftings.month = 11 THEN 'November'
                WHEN customer_liftings.month = 12 THEN 'December'
                ELSE 0 END) AS `month_name`")
                
            ]);
    }
}

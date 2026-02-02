<?php

namespace App\Repositories;

use App\Models\CustomerLifting;
use App\Repositories\BaseRepository;

class CustomerLiftingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'product_id',
        'dealer_id',
        'quantity',
        'year',
        'month',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return CustomerLifting::class;
    }
}

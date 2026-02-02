<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class MasonRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'phone',
        'aadhaar_no',
        'dob',
        'marital_status',
        'spouse_name',
        'spouse_dob',
        'address',
        'branch_id',
        'created_by',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return User::class;
    }
}

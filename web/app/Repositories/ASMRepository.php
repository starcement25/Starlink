<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class ASMRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'phone',
        'email',
        'branch_id',
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

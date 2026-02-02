<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    public $table = 'dealers';

    public $fillable = [
        'name',
        'role',
        'linked_dealer',
        'branch_id',
        'status',
        'phone',
        'whatsapp_no'
    ];

    protected $casts = [
        'name' => 'string',
        'role' => 'integer',
        'linked_dealer' => 'integer',
        'branch_id' => 'integer',
        'status' => 'integer',
        'phone' => 'string',
        'whatsapp_no' => 'string'
    ];

    public static $rules = [
        'name' => 'required|max:255',
        'role' => 'required',
        'linked_dealer' => 'required',
        'branch_id' => 'required',
        'status' => 'required',
        'phone' => 'required',
        'whatsapp_no' => 'required'
    ];

    
}

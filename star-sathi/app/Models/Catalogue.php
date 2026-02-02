<?php

namespace App\Models;

use App\Models\MasonCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalogue extends Model
{
    use HasFactory;
    protected $table ="catalogues" ;
    protected $guarded = [] ;

    public static $rules = [
        'name' => 'required|max:255',
        'description' => 'required',
        'mason_category_id' => 'required|numeric',
        'point' => 'required|min:1',
        'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
    ];


    public function mason_category()
    {
        return $this->belongsTo(MasonCategory::class, 'mason_category_id');
    }
}

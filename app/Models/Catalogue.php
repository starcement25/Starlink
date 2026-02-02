<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalogue extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 1;
    public const STATUS_DISABLE = 0;

    protected $table ="catalogues" ;
    protected $guarded = [] ;

    public static $rules = [
        'name' => 'required|max:255',
        'description' => 'required',
        'point' => 'required|min:1',
        'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
    ];

    public function getImageAttribute()
    {
        return "https://starlinkinfluencers.in/web/public/".$this->attributes['image'];
    }

    public function masonCategory()
    {
        return $this->belongsTo(MasonCategory::class, 'mason_category_id');
    }
    public function catalogueType()
    {
        return $this->belongsTo(CatalogueType::class, 'catalogue_type_id');
    }
}

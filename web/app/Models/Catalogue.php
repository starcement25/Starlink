<?php

namespace App\Models;

use App\Models\MasonCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        // 'mason_category_id' => 'required|numeric',
        'catalogue_type_id' => 'required|numeric',
        'point' => 'required|min:1',
        'img' => 'mimes:jpeg,jpg,png,gif|max:4096',
    ];
    public static $ruleAttributes = [
        // 'mason_category_id' => 'mason category',
        'catalogue_type_id' => 'catelogue type',
    ];

    public static function checkValidStatusCode($status): bool
    {
        return in_array($status, [self::STATUS_ACTIVE, self::STATUS_DISABLE]);
    }


    public function mason_category()
    {
        return $this->belongsTo(MasonCategory::class, 'mason_category_id');
    }

    public function catalogueType()
    {
        return $this->belongsTo(CatalogueType::class, 'catalogue_type_id');
    }
}

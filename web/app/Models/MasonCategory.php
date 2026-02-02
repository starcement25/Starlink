<?php

namespace App\Models;

use App\Models\Catalogue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasonCategory extends Model
{
    use HasFactory;
    protected   $guarded = [] ;
    public static $rules = [
        "name"       => "required|string|max:255",
        "from_point" => "required|numeric",
        "to_point"   => "required|numeric",
    ] ;

    public function catalogues()
    {
        return $this->hasMany(Catalogue::class, 'mason_category_id', 'id');
    }
}

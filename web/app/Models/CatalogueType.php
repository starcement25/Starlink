<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueType extends Model
{
    use HasFactory;

    protected $table = "catalogue_types";
    protected $fillable = ["name", "status"];

    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 0;

    public function catalogues(): HasMany
    {
        return $this->hasMany(Catalogue::class, "catalogue_type_id", "id");
    }
}

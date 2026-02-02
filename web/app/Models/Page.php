<?php

namespace App\Models;

use App\Models\PageContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;
    protected $guarded = [] ;


    public function contents()
    {
        return $this->hasMany(PageContent::class, 'page_id', 'id');
    }
}

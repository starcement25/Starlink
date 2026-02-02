<?php

namespace App\Models;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageContent extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    /**
     * Get all of the comments for the Page
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}

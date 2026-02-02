<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DealerLinkageRequestHistory;

class DealerLinkageRequest extends Model
{
    use HasFactory;
    protected $table = 'dealer_linkage_request';
    protected $guarded = [];

    public function dealer_linkage_request_history()
    {
        return $this->HasMany(DealerLinkageRequestHistory::class, 'dealer_linkage_request_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DealerLinkageRequest;

class DealerLinkageRequestHistory extends Model
{
    use HasFactory;
    protected $table = 'dealer_linkage_request_history';
    protected $guarded = [];

    public function dealer_linkage_request_history()
    {
        return $this->belongsTo(DealerLinkageRequest::class, 'dealer_linkage_request_id');
    }
}

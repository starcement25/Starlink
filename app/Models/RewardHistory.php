<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class RewardHistory extends Model
{

    public const ELIGIBLE_FOR_LEDGER_YES = 1; 
    public const ELIGIBLE_FOR_LEDGER_NO = 0; 

    public $table = 'reward_history';

    public $guarded = [];

    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }

    public function lifting()
    {
        return $this->belongsTo(Lifting::class, 'lifting_id');
    }

    public function mason()
    {
        return $this->belongsTo(Lifting::class, 'user_id');
    }

    public function verified_by()
    {
        return $this->belongsTo(Lifting::class, 'verified_by');
    }

    
}

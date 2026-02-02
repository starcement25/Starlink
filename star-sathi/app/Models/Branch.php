<?php

namespace App\Models;

use App\Models\User;
use App\Models\Zone;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $table = 'branch' ;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'branch_code' => 'required',
        'zone_id' => 'required',
        'state_id' => 'required',
        'description' => 'required',
        'status' => 'required',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }
    
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    

}

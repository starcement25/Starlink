<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasonLifting extends Model
{
    use HasFactory;
    protected $table = 'mason_lifting';
    protected $fillable = ['id','mason_id', 'lifting_id'];
    public function mason() {
        return $this->belongsTo('App\Models\User','mason_id');
    }
    public function lifting() {
        return $this->belongsTo('App\Models\Lifting','lifting_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class games extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'game_name'
    ];

    public function players(){
        return $this->hasMany(players::class);
    }

}

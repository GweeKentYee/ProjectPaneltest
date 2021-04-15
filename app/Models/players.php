<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class players extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'player_name',
        'games_id'
    ];

    public function games(){

        return $this->belongsTo(games::class);
        
    }

    public function player_files(){

        return $this->hasMany(player_files::class);
    }
    
}

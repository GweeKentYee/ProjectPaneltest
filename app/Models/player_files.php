<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class player_files extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'JSON_file',
        'type',
        'players_id'
    ];

    public function players(){

        return $this->belongsTo(players::class);
        
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $games_id
 * @property string $player_name
 * @property string $created_at
 * @property string $updated_at
 * @property Game $game
 * @property PlayerFile[] $playerFiles
 */
class player extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['games_id'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo('App\Models\Game', 'games_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerFiles()
    {
        return $this->hasMany('App\Models\PlayerFile', 'players_id');
    }
}

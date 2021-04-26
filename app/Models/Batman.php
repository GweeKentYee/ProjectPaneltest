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
 * @property BatmanPlayerFile[] $batmanPlayerFiles
 */
class Batman extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'batman';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['games_id', 'player_name', 'created_at', 'updated_at'];

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
    public function batmanPlayerFiles()
    {
        return $this->hasMany('App\Models\BatmanPlayerFile', 'players_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $games_id
 * @property string $data_name
 * @property string $layer
 * @property string $player_related
 * @property string $created_at
 * @property string $updated_at
 * @property Game $game
 */
class GameDataType extends Model
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
    protected $fillable = ['games_id', 'data_name', 'layer', 'player_related', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo('App\Models\Game', 'games_id');
    }
}

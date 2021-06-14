<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $users_id
 * @property string $game_name
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property GameDataType[] $gameDataTypes
 * @property Player[] $players
 */
class Game extends Model
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
    protected $fillable = ['users_id', 'game_name', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gameDataTypes()
    {
        return $this->hasMany('App\Models\GameDataType', 'games_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players()
    {
        return $this->hasMany('App\Models\Player', 'games_id');
    }
}

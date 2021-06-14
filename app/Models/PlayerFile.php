<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $players_id
 * @property string $JSON_file
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property Player $player
 */
class PlayerFile extends Model
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
    protected $fillable = ['players_id', 'file', 'type', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->belongsTo('App\Models\Player', 'players_id');
    }
}

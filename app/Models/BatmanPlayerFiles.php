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
 * @property Batman $batman
 */
class BatmanPlayerFiles extends Model
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
    protected $fillable = ['players_id', 'JSON_file', 'type', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batman()
    {
        return $this->belongsTo('App\Models\Batman', 'players_id');
    }
}

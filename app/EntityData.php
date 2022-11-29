<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityData extends Model
{
    protected $fillable = [
        'version',
        'user_id',
        'payload',
        'diff',
    ];
    
    protected $casts = [
        'version' => 'integer',
        'user_id' => 'string',
        'payload' => 'array',
        'diff'    => 'array',
    ];
    
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}

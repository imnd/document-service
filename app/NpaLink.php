<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NpaLink extends Model
{
    protected $casts = [
        'payload' => 'array',
    ];
    
    protected $fillable = [
        'npa_id'
    ];
    
    
    public function npa()
    {
        return $this->belongsTo(Npa::class);
    }
}

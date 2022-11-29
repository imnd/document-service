<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Field extends Model
{
    use HasTranslations;
    
    public $translatable = [
        'title',
        'placeholder',
        'description'
    ];
    
    protected $casts = [
        'payload'     => 'array',
        'placeholder' => 'array',
        'description' => 'array',
        'options'     => 'array',
    ];
    
    protected $fillable = [
        'title',
        'placeholder',
        'description',
        'type',
    ];

    public function groups()
    {
        return $this->belongsToMany(Field::class, 'group_fields');
    }
}

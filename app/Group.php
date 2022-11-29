<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Group extends Model
{
    use HasTranslations;
    
    public $translatable = [
        'title',
        'placeholder',
        'description',
    ];
    
    protected $casts = [
        'payload'     => 'array',
        'placeholder' => 'array',
        'description' => 'array',
        'options'     => 'array',
        'type'        => 'string',
    ];

    public $fillable = [
        'title',
        'placeholder',
        'description',
        'type',
        'options',
        'user_id',
    ];

    public function fields() {
        return $this->belongsToMany(Field::class, 'group_fields');
    }
}

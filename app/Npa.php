<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Npa extends Model
{
    use HasTranslations,
        SoftDeletes;
    
    public $translatable = ['title'];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    protected $fillable = [
        'title',
        'main_id',
    ];
    
    public function main()
    {
        return $this->belongsTo(self::class, 'main_id');
    }
    
    public function links()
    {
        return $this->hasMany(NpaLink::class);
    }
}

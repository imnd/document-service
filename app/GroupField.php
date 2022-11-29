<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupField extends Model
{

    public $table = 'user_group_fields';

    public $fillable = ['user_group_id', 'field_id', 'value'];

    public function fields() {
        return $this->belongsToMany(UserGroup::class, 'user_group_fields');
    }
}

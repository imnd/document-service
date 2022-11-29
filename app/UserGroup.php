<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserGroup extends Model
{

    public $table = 'user_group';

    public $fillable = ['group_id', 'user_id', 'group_type'];

    public function fields() {
        return $this->belongsToMany(Field::class, 'user_group_fields');
    }

    public function value_fields() {
        return $this->hasMany(GroupField::class, 'user_group_id');
    }

}

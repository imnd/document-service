<?php

use Faker\Generator as Faker,
    App\Entity;

$factory->define(Entity::class, function (Faker $faker, $attributes) {
    return [
        'type' => $attributes['type'] ?? Entity::TYPE_DOCUMENT,
        'main_id' => $attributes['main_id'] ?? null,
    ];
});

<?php

use Faker\Generator as Faker,
    App\EntityData;

$factory->define(EntityData::class, function (Faker $faker, $attributes) {
    return [
        'user_id' => $attributes['user_id'],
        'version' => $attributes['version'] ?? 1,
        'merged' => $attributes['merged'] ?? 1,
        'payload' => $attributes['payload'] ?? '{"title": {"ru": "' . $faker->title . '"}, "matrixes": [], "template_id": 6, "constructor_id": 4, "constructor_version": 1}',
    ];
});

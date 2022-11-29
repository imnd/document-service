<?php

use Faker\Generator as Faker,
    App\Field;

$factory->define(Field::class, function (Faker $faker, $attributes) {
    return [
        'type' => $attributes['type'] ?? 'input',
        'title' => $attributes['title'] ?? $faker->name,
        'placeholder' => $attributes['placeholder'] ?? $faker->text,
        'description' => $attributes['description'] ?? $faker->text,
        'options' => $attributes['options'] ?? '',
        'user_id' => $attributes['user_id'],
    ];
});

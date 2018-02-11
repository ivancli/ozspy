<?php

use Faker\Generator as Faker;

$factory->define(OzSpy\Models\Base\WebCategory::class, function (Faker $faker) {
    $categoryName = $faker->sentence(rand(1, 4));
    return [
        'name' => $categoryName,
        'slug' => $faker->boolean() ? str_slug($categoryName) : null,
        'field' => $faker->boolean(5) ? str_slug($faker->sentence(rand(1, 4))) : null,
        'url' => $faker->url,
        'active' => $faker->boolean(70),
        'last_crawled_products_count' => $faker->boolean() ? $faker->numberBetween() : null,
        'last_crawled_at' => $faker->boolean() ? $faker->dateTimeThisMonth() : null,
        'deleted_at' => $faker->boolean(5) ? $faker->dateTimeThisMonth : null,
        'created_at' => $faker->dateTimeThisMonth,
        'updated_at' => $faker->dateTimeThisMonth,
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(OzSpy\Models\Base\WebProduct::class, function (Faker $faker) {
    $productName = $faker->sentence(rand(1, 6));
    return [
        'retailer_product_id' => $faker->boolean(70) ? $faker->randomNumber() : null,
        'name' => $productName,
        'recent_price' => $faker->boolean(80) ? $faker->randomFloat(2, 0.01, 999999999.99) : null,
        'previous_price' => $faker->boolean(80) ? $faker->randomFloat(2, 0.01, 999999999.99) : null,
        'slug' => str_slug($productName),
        'url' => $faker->url,
        'brand' => $faker->boolean() ? $faker->company : null,
        'model' => $faker->boolean() ? $faker->text(255) : null,
        'sku' => $faker->boolean() ? $faker->text(255) : null,
        'gtin8' => $faker->boolean() ? $faker->text(8) : null,
        'gtin12' => $faker->boolean() ? $faker->text(12) : null,
        'gtin13' => $faker->boolean() ? $faker->text(13) : null,
        'gtin14' => $faker->boolean() ? $faker->text(14) : null,
        'last_scraped_at' => $faker->boolean() ? $faker->dateTimeThisMonth : null,
        'price_changed_at' => $faker->boolean() ? $faker->dateTimeThisMonth : null,
        'deleted_at' => $faker->boolean(5) ? $faker->dateTimeThisMonth : null,
        'created_at' => $faker->dateTimeThisMonth,
        'updated_at' => $faker->dateTimeThisMonth,
    ];
});

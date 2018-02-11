<?php

use Faker\Generator as Faker;

$factory->define(OzSpy\Models\Base\Retailer::class, function (Faker $faker) {
    $companyName = $faker->company;
    return [
        'name' => $companyName,
        'abbreviation' => strtolower(str_acronym($companyName)),
        'domain' => 'http://' . $faker->domainName,
        'ecommerce_url' => 'http://' . $faker->domainName,
        'logo' => $faker->imageUrl(),
        'active' => $faker->boolean(),
        'priority' => $faker->numberBetween(1, 10),
        'last_crawled_at' => $faker->dateTimeThisMonth,
        'deleted_at' => $faker->boolean(5) ? $faker->dateTimeThisMonth : null,
        'created_at' => $faker->dateTimeThisMonth,
        'updated_at' => $faker->dateTimeThisMonth,
    ];
});

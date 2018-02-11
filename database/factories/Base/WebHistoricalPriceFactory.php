<?php

use Faker\Generator as Faker;

$factory->define(OzSpy\Models\Base\WebHistoricalPrice::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 0.01, 999999999.99),
        'created_at' => $faker->dateTimeThisMonth,
        'updated_at' => $faker->dateTimeThisMonth,
    ];
});

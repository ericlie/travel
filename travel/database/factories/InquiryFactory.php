<?php

use App\Airport;
use App\Hotel;
use App\Inquiry;
use Faker\Generator as Faker;

$factory->define(Inquiry::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'total_packs' => $faker->numberBetween(1, 3),
        'depart_date' => $faker->dateTimeBetween('+'.rand(30, 35).' days', '+'.rand(35, 40).' days'),
        'return_date' => $faker->dateTimeBetween('+'.rand(35, 40).' days', '+'.rand(40, 45).' days'),
        'budget' => $faker->randomElement([1000, 3000, 5000]),
        // 'origin' => Airport::inRandomOrder()->first()->code,
        'origin' => 'SIN',
    ];
});

$factory->define(Hotel::class, function (Faker $faker) {
    return [
        'city_code' => $faker->randomElement(['BKK', 'HKG', 'HKT', 'MNL']),
        'name' => $faker->unique()->company. ' '. $faker->companySuffix,
        'price' => $faker->numberBetween(50, 400),
        'popularity' => $faker->numberBetween(100, 1000),
    ];
});

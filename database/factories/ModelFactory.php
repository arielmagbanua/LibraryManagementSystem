<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'middle_name' => $faker->lastName,
        'last_name' => $faker->lastName,
        'address' => $faker->address,
        'birth_date' => $faker->date('Y-m-d'),
        'account_type' => 2,
        'email' => $faker->safeEmail,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Author::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'middle_name' => $faker->lastName,
        'last_name' => $faker->lastName,
        'description' => $faker->realText($faker->numberBetween(50,100)),
        'birth_date' => $faker->date('Y-m-d')
    ];
});

$factory->define(App\Book::class, function (Faker\Generator $faker) {

    $authorIDs = App\Author::lists('id')->toArray();

    return [
        'title' => $faker->realText($faker->numberBetween(50,100)),
        'author_id' => $faker->randomElement($authorIDs),
        'isbn' => $faker->isbn13,
        'quantity' => $faker->biasedNumberBetween(0,20),
        'overdue_fine' => $faker->randomFloat(null,1.0,50.0),
        'shelf_location' => $faker->randomLetter.'-'.$faker->biasedNumberBetween(0,20)
    ];
});

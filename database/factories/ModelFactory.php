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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Department::class, function (Faker\Generator $faker) {
    return [
        'title' => ucfirst($faker->words(mt_rand(1, 3), true)),
    ];
});

$factory->define(App\Worker::class, function (Faker\Generator $faker) {
    $department = \App\Department::inRandomOrder()->first();
    if (!empty($department)) {
        $department_id = $department->id;
    } else {
        $departments = factory(App\Department::class, 5)->create();
        $department_id = $departments->random()->id;
    }
    $name = explode(' ', $faker->name);
    return [
        'name' => $name[0],
        'surname' => $name[2],
        'patronymic' => $name[1],
        'birthdate' => $faker->dateTimeBetween('-60 years', '-20 years'),
        'email' => $faker->email,
        'phone' => $faker->numerify('9#########'),
        'work_start' => $faker->dateTimeBetween('-10 years', '-5 years'),
        'work_end' => mt_rand(0, 1) ? $faker->dateTimeBetween('-3 years') : null,
        'position' => $faker->word,
        'department_id' => $department_id,
    ];
});

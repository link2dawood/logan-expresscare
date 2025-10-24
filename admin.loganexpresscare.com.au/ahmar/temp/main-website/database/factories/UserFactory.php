<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = fake()->randomElement(['active', 'inactive']);

        return [
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'personal_email' => fake()->unique()->safeEmail,
            'password' =>  '12345678',
            'user_type' => 'staff_members',
            'joining_date' => Carbon::create(2024, 4, 1)->format('Y-m-d'),
            'phone' => fake()->phoneNumber(),
            'personal_phone' => fake()->phoneNumber(),
            'status' => $status,
        ];
    }
}

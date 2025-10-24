<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => rand(100, 5000),
            'notes' => fake()->realText(),
            'date_time' => Carbon::now()->subDays(rand(1, 30)),
            'reference_number' => strtoupper(uniqid('EXP-')),
        ];
    }
}

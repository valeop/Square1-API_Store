<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usersIds = User::all()->pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($usersIds),
            'date' => $this->faker->date(),
            'total_amount'=> $this->faker->randomFloat(2, 30, 1000),
            'status' => $this->faker->randomElement(['pending', 'delivered', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['cash', 'credit card', 'debit card']),
            'shipping_address' => $this->faker->address(),
        ];
    }
}

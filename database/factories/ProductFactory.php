<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>$this->faker->randomElement(['T-shirt', 'Sweater', 'Pants', 'Shoes', 'Hat', 'Jacket', 'Socks', 'Glasses', 'Bag']),
            'description' => $this->faker->paragraph(3),
            'price' => $this->faker->randomFloat(2, 20, 100),
            'other_attributes' => json_encode([
                'gender' => $this->faker->randomElement(['female', 'male']),
                'materials' => $this->faker->randomElement(['100% cotton', '100% polyester', '80% cotton - 20% polyester', '90% cotton - 10% polyester']),
                'brand' => $this->faker->randomElement(['Calvin Klein', 'Prada', 'Gucci', 'Louis Vuitton']),
                'collection' => $this->faker->randomElement(['New arrivals', 'SQ1', 'Woman', 'Men'])])
        ];
    }
}

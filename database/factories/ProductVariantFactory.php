<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productIds = Product::all()->pluck('id')->toArray();
        return [
            'product_id' => $this->faker->randomElement($productIds),
            'color' => $this->faker->randomElement(['Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Pink', 'Navy blue', 'Beige', 'Brown', 'Grey']),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'stock_quantity' => $this->faker->numberBetween(10, 40)
        ];
    }
}

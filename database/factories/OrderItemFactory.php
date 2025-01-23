<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderIds = Order::all()->pluck('id')->toArray();
        $productVariantIds = ProductVariant::all()->pluck('id')->toArray();
        return [
            'order_id' => $this->faker->randomElement($orderIds),
            'product_variant_id' => $this->faker->randomElement($productVariantIds),
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 30, 1000),
        ];
    }
}

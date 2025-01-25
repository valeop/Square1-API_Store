<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shoppingCartId = ShoppingCart::all()->pluck('id')->toArray();
        $productVariantId = ProductVariant::all()->pluck('id')->toArray();
        return [
            'shopping_cart_id' => $this->faker->randomElement($shoppingCartId),
            'product_variant_id' => $this->faker->randomElement($productVariantId),
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 20, 500),
        ];
    }
}

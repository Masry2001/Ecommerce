<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 1000);
        return [
            'product_id' => Product::factory(),
            'sku' => 'SKU-VAR-' . strtoupper(Str::random(8)),
            'name' => $this->faker->word(),
            'options' => ['color' => $this->faker->safeColorName(), 'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL'])],
            'price' => $price,
            'compare_price' => $this->faker->optional(0.3)->randomFloat(2, $price, $price * 1.5),
            'stock_quantity' => $this->faker->numberBetween(0, 50),
            'stock_status' => 'in_stock',
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
        ];
    }
}

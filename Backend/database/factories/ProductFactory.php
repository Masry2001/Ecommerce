<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        $price = $this->faker->randomFloat(2, 10, 1000);
        
        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'price' => $price,
            'compare_price' => $this->faker->optional(0.3)->randomFloat(2, $price, $price * 1.5),
            'cost_price' => $price * 0.6,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'low_stock_threshold' => 10,
            'manage_stock' => true,
            'stock_status' => 'in_stock',
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'has_variants' => false,
            'weight' => $this->faker->randomFloat(2, 0.1, 10),
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
            'views_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->lexify('Brand ????????'),
            'description' => $this->faker->sentence(),
            'logo' => $this->faker->imageUrl(),
            'website' => $this->faker->url(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'meta_title' => $this->faker->sentence(),
            'meta_description' => $this->faker->text(200),
        ];
    }
}

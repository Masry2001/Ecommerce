<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all active products.
     */
    public function test_can_list_active_products(): void
    {
        Product::factory()->count(3)->create(['is_active' => true]);
        Product::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'stock_status',
                        'primary_image',
                        'category',
                        'brand'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    /**
     * Test filtering products by category slug.
     */
    public function test_can_filter_products_by_category(): void
    {
        $category1 = Category::factory()->create(['slug' => 'cat-1']);
        $category2 = Category::factory()->create(['slug' => 'cat-2']);

        Product::factory()->count(2)->create(['category_id' => $category1->id, 'is_active' => true]);
        Product::factory()->count(1)->create(['category_id' => $category2->id, 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?category_slug=cat-1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test filtering products by brand slug.
     */
    public function test_can_filter_products_by_brand(): void
    {
        $brand1 = Brand::factory()->create(['slug' => 'brand-1']);
        $brand2 = Brand::factory()->create(['slug' => 'brand-2']);

        Product::factory()->count(2)->create(['brand_id' => $brand1->id, 'is_active' => true]);
        Product::factory()->count(1)->create(['brand_id' => $brand2->id, 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?brand_slug=brand-1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test filtering products by featured status.
     */
    public function test_can_filter_products_by_featured(): void
    {
        Product::factory()->count(2)->create(['is_featured' => true, 'is_active' => true]);
        Product::factory()->count(3)->create(['is_featured' => false, 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?is_featured=1');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test filtering products by price range.
     */
    public function test_can_filter_products_by_price_range(): void
    {
        Product::factory()->create(['price' => 50, 'is_active' => true]);
        Product::factory()->create(['price' => 150, 'is_active' => true]);
        Product::factory()->create(['price' => 250, 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['price' => 150]);
    }

    /**
     * Test searching products by name or description.
     */
    public function test_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Specific Item', 'is_active' => true]);
        Product::factory()->create(['description' => 'Contains the keyword inside', 'is_active' => true]);
        Product::factory()->create(['name' => 'Other', 'description' => 'Other', 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?search=keyword');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $response = $this->getJson('/api/v1/products?search=Specific');
        $response->assertJsonCount(1, 'data');
    }

    /**
     * Test sorting products.
     */
    public function test_can_sort_products_by_price(): void
    {
        Product::factory()->create(['price' => 100, 'is_active' => true]);
        Product::factory()->create(['price' => 50, 'is_active' => true]);
        Product::factory()->create(['price' => 150, 'is_active' => true]);

        $response = $this->getJson('/api/v1/products?sort=price_low');

        $response->assertStatus(200);
        $this->assertEquals(50, $response->json('data.0.price'));
        $this->assertEquals(100, $response->json('data.1.price'));
        $this->assertEquals(150, $response->json('data.2.price'));

        $response = $this->getJson('/api/v1/products?sort=price_high');
        $this->assertEquals(150, $response->json('data.0.price'));
    }

    /**
     * Test getting a single product by slug.
     */
    public function test_can_show_product_by_slug(): void
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'is_active' => true
        ]);

        ProductImage::factory()->create(['product_id' => $product->id, 'is_primary' => true]);
        ProductVariant::factory()->count(2)->create(['product_id' => $product->id]);

        $response = $this->getJson('/api/v1/products/test-product');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test Product'])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'images',
                    'variants',
                    'category',
                    'brand'
                ]
            ]);
    }

    /**
     * Test showing product increments views count.
     */
    public function test_show_product_increments_views_count(): void
    {
        $product = Product::factory()->create([
            'slug' => 'test-product',
            'views_count' => 10,
            'is_active' => true
        ]);

        $this->getJson('/api/v1/products/test-product');

        $this->assertEquals(11, $product->fresh()->views_count);
    }
}

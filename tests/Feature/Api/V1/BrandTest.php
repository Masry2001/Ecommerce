<?php

namespace Tests\Feature\Api\V1;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all active brands.
     */
    public function test_can_list_active_brands(): void
    {
        Brand::factory()->count(3)->create(['is_active' => true]);
        Brand::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/brands');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'logo_url',
                        'website',
                        'sort_order',
                        'meta_title',
                        'meta_description',
                    ]
                ]
            ]);
    }

    /**
     * Test getting active brands sorted by sort_order.
     */
    public function test_brands_are_sorted_by_order(): void
    {
        Brand::factory()->create(['name' => 'Second', 'sort_order' => 20, 'is_active' => true]);
        Brand::factory()->create(['name' => 'First', 'sort_order' => 10, 'is_active' => true]);

        $response = $this->getJson('/api/v1/brands');

        $response->assertStatus(200);
        $this->assertEquals('First', $response->json('data.0.name'));
        $this->assertEquals('Second', $response->json('data.1.name'));
    }

    /**
     * Test getting a single brand by slug.
     */
    public function test_can_show_brand_by_slug(): void
    {
        $brand = Brand::factory()->create([
            'name' => 'Sony',
            'slug' => 'sony',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/v1/brands/sony');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Sony']);
    }

    /**
     * Test showing an inactive brand returns 404.
     */
    public function test_cannot_show_inactive_brand(): void
    {
        $brand = Brand::factory()->create([
            'name' => 'Inactive',
            'slug' => 'inactive',
            'is_active' => false
        ]);

        $response = $this->getJson('/api/v1/brands/inactive');

        $response->assertStatus(404);
    }
}

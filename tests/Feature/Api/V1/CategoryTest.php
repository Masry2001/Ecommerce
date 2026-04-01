<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all active categories.
     */
    public function test_can_list_active_categories(): void
    {
        Category::factory()->count(3)->create(['is_active' => true]);
        Category::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'image_url',
                        'sort_order',
                        'meta_title',
                        'meta_description',
                    ]
                ]
            ]);
    }

    /**
     * Test getting active categories sorted by sort_order.
     */
    public function test_categories_are_sorted_by_order(): void
    {
        Category::factory()->create(['name' => 'Second', 'sort_order' => 20, 'is_active' => true]);
        Category::factory()->create(['name' => 'First', 'sort_order' => 10, 'is_active' => true]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $this->assertEquals('First', $response->json('data.0.name'));
        $this->assertEquals('Second', $response->json('data.1.name'));
    }

    /**
     * Test getting a single category by slug.
     */
    public function test_can_show_category_by_slug(): void
    {
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/v1/categories/electronics');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Electronics']);
    }

    /**
     * Test showing an inactive category returns 404.
     */
    public function test_cannot_show_inactive_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Inactive',
            'slug' => 'inactive',
            'is_active' => false
        ]);

        $response = $this->getJson('/api/v1/categories/inactive');

        $response->assertStatus(404);
    }
}

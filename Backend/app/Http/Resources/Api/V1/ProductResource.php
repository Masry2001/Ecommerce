<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\ProductImageResource;
use App\Http\Resources\Api\V1\ProductVariantResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => (float) $this->price,
            'compare_price' => (float) $this->compare_price,
            'discount_percentage' => $this->compare_price > $this->price
                ? round((($this->compare_price - $this->price) / $this->compare_price) * 100)
                : 0,

            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'manage_stock' => $this->manage_stock,
            'stock_status' => $this->stock_status,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'has_variants' => $this->has_variants,
            'weight' => $this->weight,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'views_count' => $this->views_count,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'primary_image' => $this->primaryImage ? asset('storage/' . $this->primaryImage->image_path) : null,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Interfaces\ProductDocumentation;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller implements ProductDocumentation
{
    public function index(Request $request)
    {
        $query = Product::active();

        // Filtering
        if ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        if ($request->filled('brand_slug')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand_slug);
            });
        }

        if ($request->boolean('is_featured')) {
            $query->featured();
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->with(['category', 'brand', 'primaryImage'])
            ->paginate($request->get('per_page', 15));

        return ProductResource::collection($products);
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'brand', 'images', 'variants'])
            ->firstOrFail();

        // Increment views count
        $product->incrementViewsCount();

        return new ProductResource($product);
    }
}

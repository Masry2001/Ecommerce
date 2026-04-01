<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Interfaces\CategoryDocumentation;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller implements CategoryDocumentation
{
    public function index()
    {
        $categories = Category::active()
            ->sorted()
            ->get();

        return CategoryResource::collection($categories);
    }

    public function show(string $slug)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        return new CategoryResource($category);
    }
}

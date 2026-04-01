<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Interfaces\BrandDocumentation;
use App\Http\Resources\Api\V1\BrandResource;
use App\Models\Brand;

class BrandController extends Controller implements BrandDocumentation
{
    public function index()
    {
        $brands = Brand::active()
            ->sorted()
            ->get();

        return BrandResource::collection($brands);
    }

    public function show(string $slug)
    {
        $brand = Brand::active()
            ->where('slug', $slug)
            ->firstOrFail();

        return new BrandResource($brand);
    }
}

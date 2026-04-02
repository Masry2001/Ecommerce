<?php

namespace App\Http\Controllers\Api\V1\Interfaces;

use OpenApi\Attributes as OA;
use Illuminate\Http\Request;

#[OA\Schema(
    schema: "Product",
    title: "Product",
    description: "Product model",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid"),
        new OA\Property(property: "name", type: "string", example: "iPhone 15 Pro"),
        new OA\Property(property: "slug", type: "string", example: "iphone-15-pro"),
        new OA\Property(property: "sku", type: "string", example: "SKU-IPHONE15"),
        new OA\Property(property: "short_description", type: "string", nullable: true),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "price", type: "number", format: "float", example: 999.99),
        new OA\Property(property: "compare_price", type: "number", format: "float", nullable: true, example: 1099.99),
        new OA\Property(property: "discount_percentage", type: "integer", example: 10),
        new OA\Property(property: "stock_status", type: "string", example: "in_stock"),
        new OA\Property(property: "stock_quantity", type: "integer", example: 50),
        new OA\Property(property: "is_featured", type: "boolean"),
        new OA\Property(property: "views_count", type: "integer", example: 150),
        new OA\Property(property: "average_rating", type: "number", format: "float", example: 4.5),
        new OA\Property(property: "reviews_count", type: "integer", example: 12),
        new OA\Property(property: "primary_image", type: "string", format: "url", nullable: true),
        new OA\Property(
            property: "category",
            ref: "#/components/schemas/Category"
        ),
        new OA\Property(
            property: "brand",
            ref: "#/components/schemas/Brand"
        ),
        new OA\Property(property: "meta_title", type: "string", nullable: true),
        new OA\Property(property: "meta_description", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
interface ProductDocumentation
{
    #[OA\Get(
        path: "/api/v1/products",
        summary: "List all active products with filters",
        operationId: "getProducts",
        tags: ["Catalog"],
        parameters: [
            new OA\Parameter(name: "category_slug", in: "query", description: "Filter by category slug", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "brand_slug", in: "query", description: "Filter by brand slug", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "is_featured", in: "query", description: "Filter by featured status", schema: new OA\Schema(type: "boolean")),
            new OA\Parameter(name: "min_price", in: "query", description: "Filter by minimum price", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "max_price", in: "query", description: "Filter by maximum price", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "search", in: "query", description: "Keyword search in name/description", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "sort", in: "query", description: "Sort order (latest, price_low, price_high, popular)", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", description: "Number of items per page", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Product")
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request);

    #[OA\Get(
        path: "/api/v1/products/{slug}",
        summary: "Get a single product by slug",
        operationId: "getProductBySlug",
        tags: ["Catalog"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                description: "The slug of the product",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(ref: "#/components/schemas/Product")
            ),
            new OA\Response(response: 404, description: "Product not found")
        ]
    )]
    public function show(string $slug);
}

<?php

namespace App\Http\Controllers\Api\V1\Interfaces;
 
use OpenApi\Attributes as OA;
 
#[OA\Schema(
    schema: "Category",
    title: "Category",
    description: "Category model",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid"),
        new OA\Property(property: "name", type: "string", example: "Electronics"),
        new OA\Property(property: "slug", type: "string", example: "electronics"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "image_url", type: "string", format: "url", nullable: true),
        new OA\Property(property: "is_active", type: "boolean"),
        new OA\Property(property: "sort_order", type: "integer", example: 1),
        new OA\Property(property: "meta_title", type: "string", nullable: true),
        new OA\Property(property: "meta_description", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
interface CategoryDocumentation
{
    #[OA\Get(
        path: "/api/v1/categories",
        summary: "List all active categories",
        operationId: "getCategories",
        tags: ["Catalog"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Category")
                )
            )
        ]
    )]
    public function index();

    #[OA\Get(
        path: "/api/v1/categories/{slug}",
        summary: "Get a single category by slug",
        operationId: "getCategoryBySlug",
        tags: ["Catalog"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                description: "The slug of the category",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(ref: "#/components/schemas/Category")
            ),
            new OA\Response(response: 404, description: "Category not found"),
        ]
    )]
    public function show(string $slug);
}

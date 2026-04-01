<?php

namespace App\Http\Controllers\Api\V1\Interfaces;
 
use OpenApi\Attributes as OA;
 
#[OA\Schema(
    schema: "Brand",
    title: "Brand",
    description: "Brand model",
    properties: [
        new OA\Property(property: "id", type: "string", format: "uuid"),
        new OA\Property(property: "name", type: "string", example: "Sony"),
        new OA\Property(property: "slug", type: "string", example: "sony"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "logo_url", type: "string", format: "url", nullable: true),
        new OA\Property(property: "website", type: "string", format: "url", nullable: true),
        new OA\Property(property: "is_active", type: "boolean"),
        new OA\Property(property: "sort_order", type: "integer", example: 1),
        new OA\Property(property: "meta_title", type: "string", nullable: true),
        new OA\Property(property: "meta_description", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
    ]
)]
interface BrandDocumentation
{
    #[OA\Get(
        path: "/api/v1/brands",
        summary: "List all active brands",
        operationId: "getBrands",
        tags: ["Catalog"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Brand")
                )
            )
        ]
    )]
    public function index();

    #[OA\Get(
        path: "/api/v1/brands/{slug}",
        summary: "Get a single brand by slug",
        operationId: "getBrandBySlug",
        tags: ["Catalog"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                description: "The slug of the brand",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(ref: "#/components/schemas/Brand")
            ),
            new OA\Response(response: 404, description: "Brand not found"),
        ]
    )]
    public function show(string $slug);
}

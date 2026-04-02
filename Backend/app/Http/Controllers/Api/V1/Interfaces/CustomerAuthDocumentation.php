<?php

namespace App\Http\Controllers\Api\V1\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\V1\RegisterCustomerRequest;
use App\Http\Requests\Api\V1\LoginCustomerRequest;
use OpenApi\Attributes as OA;

interface CustomerAuthDocumentation
{

    #[OA\Post(
        path: "/api/v1/register",
        summary: "Register a new customer",
        operationId: "register",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
                    new OA\Property(property: "gender", type: "string", enum: ["male", "female", "other"]),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Customer registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                        new OA\Property(property: "customer", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function register(RegisterCustomerRequest $request): JsonResponse;

    #[OA\Post(
        path: "/api/v1/login",
        summary: "Login a customer",
        operationId: "login",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "access_token", type: "string"),
                        new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
        ]
    )]
    public function login(LoginCustomerRequest $request): JsonResponse;

    #[OA\Post(
        path: "/api/v1/logout",
        summary: "Logout a customer",
        operationId: "logout",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successfully logged out",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Successfully logged out."),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function logout(Request $request): JsonResponse;

    #[OA\Get(
        path: "/api/v1/customer",
        summary: "Get the authenticated customer profile",
        operationId: "getCustomer",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function customer(Request $request): JsonResponse;
}

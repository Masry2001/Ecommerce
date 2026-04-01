<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "E-commerce API",
    description: "API documentation for the storefront and customer operations.",
    contact: new OA\Contact(email: "support@example.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Use the token received from the /login endpoint."
)]
abstract class Controller
{
    //
}

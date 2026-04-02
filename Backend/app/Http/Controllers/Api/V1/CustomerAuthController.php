<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\V1\RegisterCustomerRequest;
use App\Http\Requests\Api\V1\LoginCustomerRequest;

use App\Http\Controllers\Api\V1\Interfaces\CustomerAuthDocumentation;

class CustomerAuthController extends Controller implements CustomerAuthDocumentation
{
    public function register(RegisterCustomerRequest $request): JsonResponse
    {

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'customer' => $customer,
        ], 201);
    }

    public function login(LoginCustomerRequest $request): JsonResponse
    {

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if (!$customer->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'customer' => $customer,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        // By default, Sanctum attaches the user to $request->user() regardless of the model name
        // so use the $request->user() not $request->customer().

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }

    public function customer(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}

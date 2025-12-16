<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;    
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * RegisterController handles user registration and login for the API.
 * It extends BaseController to use standardized API response methods.
 */
class RegisterController extends BaseController
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required',                    // Name is required
            'email' => 'required|email|unique:users', // Must be unique email
            'password' => 'required|confirmed'      // Password must be confirmed
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Encrypt password
        ]);

        // Generate Sanctum API token
        $token = $user->createToken('API Token')->plainTextToken;

        // Return success response with token and user name
        return $this->sendResponse([
            'token' => $token,
            'name' => $user->name
        ], 'User registered successfully');
    }

    /**
     * Login existing user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Attempt to authenticate user with email and password
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Generate Sanctum API token
            $token = $user->createToken('API Token')->plainTextToken;

            // Return success response with token and user name
            return $this->sendResponse([
                'token' => $token,
                'name' => $user->name
            ], 'User logged in successfully');
        }

        // Return error response if authentication fails
        return $this->sendError('Unauthorized', ['error' => 'Invalid credentials']);
    }
}

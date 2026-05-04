<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *    title="Laravel 12 API Documentation",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_logged_in' => true
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'name' => $user->name
        ], 'User registered successfully');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $user->update([
                'is_logged_in' => true
            ]);

            $token = $user->createToken('API Token')->plainTextToken;

            return $this->sendResponse([
                'token' => $token,
                'name' => $user->name
            ], 'User logged in successfully');
        }

        return $this->sendError('Unauthorized', ['error' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->update([
            'is_logged_in' => false,
            'last_logout_at' => now()
        ]);

        $user->currentAccessToken()->delete();

        return $this->sendResponse([], 'Logged out successfully');
    }

    public function logoutAllDevices(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $user->update([
            'is_logged_in' => false,
            'last_logout_at' => now()
        ]);

        return $this->sendResponse([], 'Logged out from all devices successfully');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        if (!Hash::check($request->old_password, $request->user()->password)) {
            return $this->sendError('Error', ['error' => 'Old password is incorrect']);
        }

        $request->user()->update([
            'password' => Hash::make($request->new_password),
            'last_password_changed_at' => now()
        ]);

        return $this->sendResponse([], 'Password changed successfully');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return $this->sendResponse($user, 'Profile updated successfully');
    }

    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path('avatars/' . $user->avatar))) {
                unlink(public_path('avatars/' . $user->avatar));
            }

            $fileName = time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('avatars'), $fileName);

            $user->update(['avatar' => $fileName]);
        }

        return $this->sendResponse(['avatar_url' => asset('avatars/' . $user->avatar)], 'Avatar updated successfully');
    }
}
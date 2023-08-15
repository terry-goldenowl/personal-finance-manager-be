<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                "name" => ['required', 'string', 'max:50'],
                "email" => ['required', 'string', 'email', 'unique:' . User::class],
                "password" => ['required', 'confirmed', 'min:8', 'max:30', Rules\Password::defaults()]
            ]);

            $userData = [
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => Hash::make($fields['password'])
            ];

            $newUser = UserServices::create($userData);

            if (!$newUser) {
                return response()->json(['error' => 'Can not register new user!', 400]);
            }

            $token = $newUser->createToken('somedummytoken')->plainTextToken;

            return response()->json([
                'user' => $newUser,
                'token' => $token
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Something went wrong when registering!',
                'error' => $error
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $fields = $request->validate([
                "email" => ['required', 'string', 'email'],
                "password" => ['required', Rules\Password::defaults()]
            ]);

            $user = UserServices::getUserByEmail($fields['email']);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if (!Hash::check($fields['password'], $user->password)) {
                return response()->json(['message' => 'Password is not correct!'], 400);
            }

            $token = $user->createToken('somedummytoken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Something went wrong when loging in!',
                'error' => $error
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logged out successfully!'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Something went wrong when loging out!',
                'error' => $error
            ]);
        }
    }
}

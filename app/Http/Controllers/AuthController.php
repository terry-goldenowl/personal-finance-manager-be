<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\User;
use App\Services\UserServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    function __construct(private UserServices $userServices)
    {
    }
    private function generateVerificationCode($length = 6)
    {
        $characters = '0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => ['required', 'string', 'max:50'],
                "email" => ['required', 'string', 'email', 'unique:' . User::class],
                "password" => ['required', 'confirmed', 'min:8', 'max:30', Rules\Password::defaults()]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'error' =>  $validator->errors()
                ]);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];

            $newUser = $this->userServices->create($userData);

            if (!$newUser) {
                return response()->json(['error' => 'Can not register new user!'], 400);
            }

            $verificationCode = $this->generateVerificationCode();

            // Save user's verification code to db
            $newUser->verification_code = $verificationCode;
            $newUser->save();

            Mail::to($newUser)->send(new EmailVerification($verificationCode));

            return response()->json([
                'message' => 'Email verification code was sent'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Something went wrong when registering!',
                'error' => $error
            ], 404);
        }
    }

    public function verify(Request $request)
    {
        try {
            $email = $request->get('email');
            $verificationCode = $request->get('verification_code');

            $user = User::where(['email' => $email, 'verification_code' => $verificationCode])->first();

            if ($user) {
                $user->is_verified = 1;
                $user->save();

                return response()->json([
                    'message' => 'Verification code is correct'
                ]);
            } else {
                return response()->json([
                    'message' => 'Verification code is incorrect',
                    'error' => 'error'
                ]);
            }
        } catch (Exception $error) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $error
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email" => ['required', 'string', 'email'],
                "password" => ['required', Rules\Password::defaults()]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'error' => 'error'
                ], 404);
            }

            $user = $this->userServices->getUserByEmail($request->email);

            if (!$user) {
                return response()->json(['message' => 'User not found!'], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
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

    public function resetPassword(Request $request)
    {
    }

    public function showResetPassword(Request $request,)
    {
    }

    public function forgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        if (!User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'User with this email does not exist!',
                'error' => 'error'
            ]);
        }

        //  Generate random roken
        $token = Str::random(40);

        $resetLink = env('APP_FE_URL') . "/reset-password/" . $token;

        Mail::to($request->only('email'))->send(new PasswordReset($resetLink));

        return response()->json([
            'message' => 'Password reset link was sent!',
        ]);
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

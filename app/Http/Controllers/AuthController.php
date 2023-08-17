<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\User;
use App\Services\UserServices;
use Exception;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

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
                    'status' => 'fail',
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
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Can not register new user!'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Register user successfully!'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
                'error' => $error
            ], 404);
        }
    }

    public function sendVerificationCode(Request $request)
    {
        try {
            $verificationCode = $this->generateVerificationCode();

            $user = User::where('email', $request->email)->first();

            // Save user's verification code to db
            $user->verification_code = $verificationCode;
            $user->save();

            Mail::to($user)->send(new EmailVerification($verificationCode));

            return response()->json([
                'status' => 'success',
                'message' => 'Email verification code was sent'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
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
                    'status' => 'success',
                    'message' => 'Verification code is correct'
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Verification code is incorrect'
                ]);
            }
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
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
                    'status' => 'fail',
                    'error' => 'Invalid credentials'
                ]);
            }

            $user = $this->userServices->getUserByEmail($request->email);

            if (!$user) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'User not found!'
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Password is not correct!'
                ]);
            }

            // Check if user has verified email
            if ($user->is_verified == 0) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'Email hasn\'t been verified yet!'
                ]);
            }

            // $credentials = $request->only('email', 'password');
            // $remember = $request->has('remember');

            // return response()->json([
            //     $credentials
            // ]);
            $token = $user->createToken(env('AUTH_TOKEN'))->plainTextToken;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Something went wrong when login user!'
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
                'newPassword' => 'required|string|confirmed|min:8',
                'email' => 'required|string|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'error' =>  $validator->errors()
                ]);
            }

            $user = User::where('email', $request->email)->first();

            $tokenExists = app(PasswordBroker::class)->tokenExists($user, $request->token);

            if ($tokenExists) {
                $user->password = Hash::make($request->newPassword);
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Password is updated!'
                ]);
            } else {
                return
                    response()->json([
                        'status' => 'fail',
                        'message' => 'Token is invalid!'
                    ]);
            }
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
                'error' => $error
            ]);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            if (!User::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'User with this email does not exist!'
                ]);
            }

            //  Generate random roken
            // $token = Str::random(40);
            $token = app(PasswordBroker::class)->createToken(User::where('email', $request->email)->first());

            $resetLink = env('APP_FE_URL') . "/reset-password/" . $token;

            Mail::to($request->only('email'))->send(new PasswordReset($resetLink));

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link was sent!',
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
                'error' => $error
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully!'
            ]);
        } catch (Exception $error) {
            return response()->json([
                'status' => 'fail',
                'error' => $error
            ]);
        }
    }
}

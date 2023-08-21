<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\User;
use App\Services\UserServices;
use Exception;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthHelper
{

    protected $userServices;
    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
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
                return ReturnType::fail($validator->errors());
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];

            $newUser = $this->userServices->create($userData);

            if (!$newUser) {
                return ReturnType::fail($validator->errors());
            }

            return ReturnType::success('Register user successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function sendVerificationCode(Request $request)
    {
        try {
            $verificationCode = $this->generateVerificationCode();

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ReturnType::fail('User with this email not found!');
            }

            // Save user's verification code to db
            $user->verification_code = $verificationCode;
            $user->save();

            Mail::to($user)->send(new EmailVerification($verificationCode));

            return ReturnType::success('Email verification code was sent!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
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

                return ReturnType::success("Verification code is correct!");
            } else {
                return ReturnType::fail("Verification code is incorrect!");
            }
        } catch (Exception $error) {
            return ReturnType::fail($error);
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
                return ReturnType::fail("Invalid credentials!");
            }

            $user = $this->userServices->getUserByEmail($request->email);

            if (!$user) {
                return ReturnType::fail("User not found!");
            }

            if (!Hash::check($request->password, $user->password)) {
                return ReturnType::fail('Password is not correct!');
            }

            // Check if user has verified email
            if ($user->is_verified == 0) {
                return ReturnType::fail("Email hasn\'t been verified yet!");
            }

            $token = $user->createToken(env('AUTH_TOKEN'))->plainTextToken;

            return ReturnType::success('Login successfully!', [
                'user' => $user,
                'token' => $token
            ]);
        } catch (Exception $error) {
            return ReturnType::fail('Something went wrong when login user!');
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
                return ReturnType::fail($validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            $tokenExists = app(PasswordBroker::class)->tokenExists($user, $request->token);

            if ($tokenExists) {
                $user->password = Hash::make($request->newPassword);
                $user->save();

                return ReturnType::success("Password is updated!");
            } else {
                return ReturnType::fail("Token is invalid!");
            }
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            if (!User::where('email', $request->email)->exists()) {
                return ReturnType::fail('User with this email does not exist!');
            }

            //  Generate random roken
            // $token = Str::random(40);
            $token = app(PasswordBroker::class)->createToken(User::where('email', $request->email)->first());

            $resetLink = env('APP_FE_URL') . "/reset-password/" . $token;

            Mail::to($request->only('email'))->send(new PasswordReset($resetLink));

            return ReturnType::success('Password reset link was sent!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();

            return ReturnType::success('Logged out successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}

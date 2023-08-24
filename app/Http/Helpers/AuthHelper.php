<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterUserRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Services\UserServices;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

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

    public function register(RegisterUserRequest $request)
    {
        try {
            $validated = $request->safe()->only(['name', 'email', 'password']);
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ];

            $newUser = $this->userServices->create($userData);

            if (!$newUser) {
                return ReturnType::fail('Register user fail!');
            }

            return ReturnType::success('Register user successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function sendVerificationCode(Request $request)
    {
        try {
            $user = $this->userServices->getUserByEmail($request->email);

            if (!$user) {
                return ReturnType::fail('User with this email not found!');
            }

            $verificationCode = $this->generateVerificationCode();
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

            $user = $this->userServices->checkExistsByEmailAndCode($email, $verificationCode);

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

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->safe()->only(['email', 'password']);

            $user = $this->userServices->getUserByEmail($validated['email']);

            if (!$user) {
                return ReturnType::fail("User not found!");
            }

            if (!Hash::check($validated['password'], $user->password)) {
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

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $validated = $request->safe()->only(['token', 'email', 'newPassword']);

            $user = $this->userServices->getUserByEmail($validated['email']);
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

            if (!$this->userServices->checkExistsByEmail($request->email)) {
                return ReturnType::fail('User with this email does not exist!');
            }

            $token = app(PasswordBroker::class)->createToken($this->userServices->getUserByEmail($request->email));

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

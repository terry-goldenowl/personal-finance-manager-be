<?php

namespace App\Http\Services;

use App\Helpers\ReturnType;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class AuthService extends BaseService
{
    protected $verificationCodeServices;
    protected $userServices;

    public function __construct(
        VerificationCodeServices $verificationCodeServices,
        UserServices $userServices
    ) {
        parent::__construct(User::class);
        $this->verificationCodeServices = $verificationCodeServices;
        $this->userServices = $userServices;
    }

    private function _generateVerificationCode(int $length = 6): string
    {
        $characters = '0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function register(array $data): array
    {
        try {
            $data['password'] = Hash::make($data['password']);

            $newUser = $this->model::create($data);

            if (!$newUser) {
                return ReturnType::fail('Register user fail!');
            }

            return ReturnType::success('Register user successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function sendVerificationCode(string $email): array
    {
        try {
            $verificationCode = $this->_generateVerificationCode();

            $this->verificationCodeServices->createOrUpdate($email, $verificationCode);

            Mail::to($email)->send(new EmailVerification($verificationCode));

            return ReturnType::success('Email verification code was sent!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function verify(string $email, string $verificationCode): array
    {
        try {
            $isCorrectCode = $this->verificationCodeServices->checkExists($email, $verificationCode);

            if ($isCorrectCode) {
                return ReturnType::success("Verification code is correct!");
            } else {
                return ReturnType::fail("Verification code is incorrect!");
            }
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function login($data): array
    {
        try {
            $user = $this->userServices->getUserByEmail($data['email']);

            if (!$user) {
                return ReturnType::fail("User not found!");
            }

            if (!Hash::check($data['password'], $user->password)) {
                return ReturnType::fail('Password is not correct!');
            }

            $token = $user->createToken(env('AUTH_TOKEN'))->plainTextToken;

            return ReturnType::success(
                'Login successfully!',
                [
                    'user' => $user,
                    'token' => $token
                ]
            );
        } catch (Exception $error) {
            return ReturnType::fail('Something went wrong when login user!');
        }
    }

    public function resetPassword($data): array
    {
        try {
            $user = $this->userServices->getUserByEmail($data['email']);
            $tokenExists = app(PasswordBroker::class)->tokenExists($user, $data['token']);

            if ($tokenExists) {
                $user->password = Hash::make($data['newPassword']);
                $user->save();

                return ReturnType::success("Password is updated!");
            } else {
                return ReturnType::fail("Token is invalid!");
            }
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function forgetPassword(string $email): array
    {
        try {
            if (!$this->userServices->checkExistsByEmail($email)) {
                return ReturnType::fail('User with this email does not exist!');
            }

            $user = $this->userServices->getUserByEmail($email);

            $token = app(PasswordBroker::class)->createToken($user);

            $resetLink = env('APP_FE_URL') . "/reset-password/" . $token;

            Mail::to($user)->send(new PasswordReset($resetLink));

            return ReturnType::success('Password reset link was sent!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function logout(User $user): array
    {
        try {
            $user->tokens()->delete();

            return ReturnType::success('Logged out successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}

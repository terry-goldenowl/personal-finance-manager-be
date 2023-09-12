<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterUserRequest;
use App\Http\Requests\Users\ResetPasswordRequest;
use App\Http\Services\AuthService;

class AuthController extends Controller
{
    function __construct(private AuthService $authService)
    {
    }

    public function register(RegisterUserRequest $request)
    {
        $returnData = $this->authService->register($request->validated());
        return ReturnType::response($returnData);
    }

    public function sendVerificationCode(Request $request)
    {
        $returnData = $this->authService->sendVerificationCode($request->email);
        return ReturnType::response($returnData);
    }

    public function verify(Request $request)
    {
        $returnData = $this->authService->verify($request->email, $request->verification_code);
        return ReturnType::response($returnData);
    }

    public function login(LoginRequest $request)
    {
        $returnData = $this->authService->login($request->validated());
        return ReturnType::response($returnData);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $returnData = $this->authService->resetPassword($request->validated());
        return ReturnType::response($returnData);
    }

    public function forgetPassword(Request $request)
    {
        $returnData = $this->authService->forgetPassword($request->email);
        return ReturnType::response($returnData);
    }

    public function logout(Request $request)
    {
        $returnData = $this->authService->logout($request->user());
        return ReturnType::response($returnData);
    }
}

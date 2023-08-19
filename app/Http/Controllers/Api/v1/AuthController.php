<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helper\AuthHelper;

class AuthController extends Controller
{
    private AuthHelper $authHelper;

    function __construct(AuthHelper $authHelper)
    {
        $this->authHelper = $authHelper;
    }

    public function register(Request $request)
    {
        $returnData = $this->authHelper->register($request);
        return ReturnType::response($returnData);
    }

    public function sendVerificationCode(Request $request)
    {
        $returnData = $this->authHelper->sendVerificationCode($request);
        return ReturnType::response($returnData);
    }

    public function verify(Request $request)
    {
        $returnData = $this->authHelper->verify($request);
        return ReturnType::response($returnData);
    }

    public function login(Request $request)
    {
        $returnData = $this->authHelper->login($request);
        return ReturnType::response($returnData);
    }

    public function resetPassword(Request $request)
    {
        $returnData = $this->authHelper->resetPassword($request);
        return ReturnType::response($returnData);
    }

    public function forgetPassword(Request $request)
    {
        $returnData = $this->authHelper->forgetPassword($request);
        return ReturnType::response($returnData);
    }

    public function logout(Request $request)
    {
        $returnData = $this->authHelper->logout($request);
        return ReturnType::response($returnData);
    }
}

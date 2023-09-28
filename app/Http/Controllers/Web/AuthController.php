<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AuthHelper;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthHelper $authHelper;

    public function __construct(AuthHelper $authHelper)
    {
        $this->authHelper = $authHelper;
    }

    public function register(Request $request)
    {
        try {
            $this->authHelper->register($request);
        } catch (Exception $error) {
        }
    }
}

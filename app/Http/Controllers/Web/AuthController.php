<?php

namespace App\Http\Controllers\Web;


use Exception;

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
        try {
            $this->authHelper->register($request);
        } catch (Exception $error) {
        }
    }
}

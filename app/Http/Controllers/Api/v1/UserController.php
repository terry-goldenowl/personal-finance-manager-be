<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UserHelper;
use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Services\UserServices;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function __construct(private UserServices $userServices)
    {
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $returnData = $this->userServices->updateUser($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $returnData = $this->userServices->updatePassword($request->user()->id, $request->validated());
        return ReturnType::response($returnData);
    }

    public function deleteUser(Request $request)
    {
        $returnData = $this->userServices->delete($request->user()->id);
        return ReturnType::response($returnData);
    }

    public function getAll(Request $request)
    {
        $returnData = $this->userServices->getUsers($request->all());
        return ReturnType::response($returnData);
    }
}

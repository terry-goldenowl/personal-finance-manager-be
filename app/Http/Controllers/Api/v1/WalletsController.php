<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\WalletsHelper;
use App\Http\Requests\Wallets\CreateWalletRequest;
use Illuminate\Http\Request;


class WalletsController extends Controller
{
    public function __construct(private WalletsHelper $walletHelper)
    {
    }

    public function create(CreateWalletRequest $request)
    {
        // return Auth::check();
        $returnData = $this->walletHelper->create($request);
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->walletHelper->get($request);
        return ReturnType::response($returnData);
    }

    public function update(CreateWalletRequest $request, int $id)
    {
        $returnData = $this->walletHelper->update($request, $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->walletHelper->delete($request, $id);
        return ReturnType::response($returnData);
    }
}

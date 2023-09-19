<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\WalletsHelper;
use App\Http\Requests\Wallets\CreateWalletRequest;
use App\Http\Requests\Wallets\UpdateWalletRequest;
use App\Http\Services\WalletServices;
use Illuminate\Http\Request;


class WalletsController extends Controller
{
    public function __construct(private WalletServices $walletServices)
    {
    }

    public function create(CreateWalletRequest $request)
    {
        $returnData = $this->walletServices->create($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->walletServices->get($request->user());
        return ReturnType::response($returnData);
    }

    public function update(UpdateWalletRequest $request, int $id)
    {
        $returnData = $this->walletServices->update($request->validated(), $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->walletServices->delete($id);
        return ReturnType::response($returnData);
    }
}

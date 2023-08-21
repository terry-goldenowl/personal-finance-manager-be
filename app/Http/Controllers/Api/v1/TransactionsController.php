<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\TransactionsHelper;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function __construct(private TransactionsHelper $transactionsHelper)
    {
    }

    public function create(CreateTransactionRequest $request)
    {
        $returnData = $this->transactionsHelper->create($request);
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->transactionsHelper->get($request);
        return ReturnType::response($returnData);
    }

    // public function update(CreateTransactionRequest $request, int $id)
    // {
    //     $returnData = $this->transactionsHelper->update($request, $id);
    //     return ReturnType::response($returnData);
    // }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->transactionsHelper->delete($request, $id);
        return ReturnType::response($returnData);
    }
}

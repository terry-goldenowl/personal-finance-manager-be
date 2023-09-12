<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\CreateTransactionRequest;
use App\Http\Requests\Transactions\UpdateTransactionRequest;
use App\Http\Services\TransactionServices;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function __construct(private TransactionServices $transactionServices)
    {
    }

    public function create(CreateTransactionRequest $request)
    {
        $returnData = $this->transactionServices->create($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->transactionServices->get($request->user(), $request->all());
        return ReturnType::response($returnData);
    }

    public function update(UpdateTransactionRequest $request, int $id)
    {
        $returnData = $this->transactionServices->update($request->user(), $request->validated(), $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->transactionServices->delete($id);
        return ReturnType::response($returnData);
    }
}

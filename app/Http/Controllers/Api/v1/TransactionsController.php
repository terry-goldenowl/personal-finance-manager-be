<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\MyResponse;
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

        return (new MyResponse($returnData))->get();
    }

    public function get(Request $request)
    {
        $returnData = $this->transactionServices->get($request->user(), $request->all());

        return (new MyResponse($returnData))->get();
    }

    public function getYears(Request $request)
    {
        $returnData = $this->transactionServices->getYears($request->user(), $request->all());

        return (new MyResponse($returnData))->get();
    }

    public function getCounts(Request $request)
    {
        $returnData = $this->transactionServices->count();

        return (new MyResponse($returnData))->get();
    }

    public function update(UpdateTransactionRequest $request, int $id)
    {
        $returnData = $this->transactionServices->update($request->user(), $request->validated(), $id);

        return (new MyResponse($returnData))->get();
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->transactionServices->delete($id);

        return (new MyResponse($returnData))->get();
    }
}

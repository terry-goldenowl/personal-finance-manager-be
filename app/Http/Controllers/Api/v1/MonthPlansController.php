<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\CreateMonthPlanRequest;
use App\Http\Requests\Plans\UpdatePlanRequest;
use App\Http\Services\MonthPlanService;
use Illuminate\Http\Request;

class MonthPlansController extends Controller
{
    public function __construct(private MonthPlanService $monthPlanService)
    {
    }

    public function create(CreateMonthPlanRequest $request)
    {
        $returnData = $this->monthPlanService->create($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->monthPlanService->get($request->user(), $request->all());
        return ReturnType::response($returnData);
    }

    public function update(UpdatePlanRequest $request, int $id)
    {
        $returnData = $this->monthPlanService->update($request->user(), $request->validated(), $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->monthPlanService->delete($id);
        return ReturnType::response($returnData);
    }
}

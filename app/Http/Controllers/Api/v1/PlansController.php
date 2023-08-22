<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\PlansHelper;
use App\Http\Requests\Plans\CreateCategoryPlanRequest;
use App\Http\Requests\Plans\CreateMonthPlanRequest;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function __construct(private PlansHelper $plansHelper)
    {
    }

    public function createMonthPlan(CreateMonthPlanRequest $request)
    {
        $returnData = $this->plansHelper->createMonthPlan($request);
        return ReturnType::response($returnData);
    }

    public function createCategoryPlan(CreateCategoryPlanRequest $request)
    {
        $returnData = $this->plansHelper->createCategoryPlan($request);
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->plansHelper->get($request);
        return ReturnType::response($returnData);
    }

    public function updateMonthPlan(CreateMonthPlanRequest $request, int $id)
    {
        $returnData = $this->plansHelper->updateMonthPlan($request, $id);
        return ReturnType::response($returnData);
    }

    public function updateCategoryPlan(CreateCategoryPlanRequest $request, int $id)
    {
        $returnData = $this->plansHelper->updateCategoryPlan($request, $id);
        return ReturnType::response($returnData);
    }

    public function deleteMonthPlan(Request $request, int $id)
    {
        $returnData = $this->plansHelper->deleteMonthPlan($request, $id);
        return ReturnType::response($returnData);
    }

    public function deleteCategoryPlan(Request $request, int $id)
    {
        $returnData = $this->plansHelper->deleteCategoryPlan($request, $id);
        return ReturnType::response($returnData);
    }
}

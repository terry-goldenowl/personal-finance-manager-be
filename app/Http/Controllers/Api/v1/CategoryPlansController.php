<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\CreateCategoryPlanRequest;
use App\Http\Requests\Plans\UpdatePlanRequest;
use App\Http\Services\CategoryPlanService;
use Illuminate\Http\Request;

class CategoryPlansController extends Controller
{
    public function __construct(private CategoryPlanService $categoryPlanService)
    {
    }

    public function create(CreateCategoryPlanRequest $request)
    {
        $returnData = $this->categoryPlanService->create($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->categoryPlanService->get($request->user(), $request->all());
        return ReturnType::response($returnData);
    }

    public function update(UpdatePlanRequest $request, int $id)
    {
        $returnData = $this->categoryPlanService->update($request->user(), $request->validated(), $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->categoryPlanService->delete($id);
        return ReturnType::response($returnData);
    }
}

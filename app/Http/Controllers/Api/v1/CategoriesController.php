<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Http\Services\CategoryServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{


    public function __construct(private CategoryServices $categoryServices)
    {
    }

    public function create(CreateCategoryRequest $request)
    {
        $returnData = $this->categoryServices->create($request->user(), $request->validated());
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->categoryServices->get($request->user(), $request->all());
        return ReturnType::response($returnData);
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        $returnData = $this->categoryServices->update($request->validated(), $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->categoryServices->delete($id);
        return ReturnType::response($returnData);
    }
}

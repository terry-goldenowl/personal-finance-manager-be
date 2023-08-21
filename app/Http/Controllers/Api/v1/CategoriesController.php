<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ReturnType;
use App\Http\Controllers\Controller;
use App\Http\Helpers\CategoriesHelper;
use App\Http\Requests\Categories\CreateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{


    public function __construct(private CategoriesHelper $categoryHelper)
    {
    }

    public function create(CreateCategoryRequest $request)
    {
        // return Auth::check();
        $returnData = $this->categoryHelper->create($request);
        return ReturnType::response($returnData);
    }

    public function get(Request $request)
    {
        $returnData = $this->categoryHelper->get($request);
        return ReturnType::response($returnData);
    }

    public function update(CreateCategoryRequest $request, int $id)
    {
        $returnData = $this->categoryHelper->update($request, $id);
        return ReturnType::response($returnData);
    }

    public function delete(Request $request, int $id)
    {
        $returnData = $this->categoryHelper->delete($request, $id);
        return ReturnType::response($returnData);
    }
}
<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Services\CategoryServices;
use Exception;
use Illuminate\Http\Request;

class CategoriesHelper
{
    private CategoryServices $categoryServices;
    public function __construct(CategoryServices $services)
    {
        $this->categoryServices = $services;
    }

    public function create(CreateCategoryRequest $request)
    {
        try {
            $validated = $request->safe()->only(['name', 'image', 'type']);

            $categoryData = array_merge($validated, ['user_id' => $request->user()->id]);
            $newCategory = $this->categoryServices->create($categoryData);

            if (!$newCategory) {
                return ReturnType::fail('This category has been created before by this user!');
            }

            return ReturnType::success('Create category successfully!', ['category' => $newCategory]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(Request $request)
    {
        try {
            $type = $request->has('type') ? $request->input('type') : null;
            $categories = $request->user()->categories();
            if ($type) {
                $categories->where("type", $type);
            }
            return ReturnType::success("Get categories successfully", ['categories' => $categories->get()]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(CreateCategoryRequest $request, int $id)
    {
        try {
            $validated = $request->safe()->only(['name', 'image', 'type']);

            $updated = $this->categoryServices->update($validated, $id);

            if (!!!$updated) {
                return ReturnType::fail('Update fails or category not found!');
            }

            return ReturnType::success('Update category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
    public function delete(Request $request, int $id)
    {
        try {
            $deleted = $this->categoryServices->delete($id);
            if (!$deleted) {
                return ReturnType::fail('Delete fails or category not found!');
            }

            return ReturnType::success('Delete category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}

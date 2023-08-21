<?php

namespace App\Http\Helpers;

use App\Helpers\ReturnType;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoriesHelper
{
    public function create(CreateCategoryRequest $request)
    {
        try {
            $validated = $request->safe()->only(['name', 'image', 'type']);

            if (Category::where(['user_id' => $request->user()->id, 'name' => $validated['name']])->exists()) {
                return ReturnType::fail('This category has been created before by this user!');
            }

            $newCategory = Category::create(array_merge($validated, ['user_id' => $request->user()->id]));

            return ReturnType::success('Create category successfully!', ['category' => $newCategory]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function get(Request $request)
    {
        try {
            $categories = $request->user()->categories()->get();
            return ReturnType::success("Get categories successfully", ['categories' => $categories]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }

    public function update(CreateCategoryRequest $request, int $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return ReturnType::fail('Category not found!');
            }

            $validated = $request->safe()->only(['name', 'image', 'type']);
            $category->update($validated);

            return ReturnType::success('Update category successfully!', ['category' => $category]);
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
    public function delete(Request $request, int $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return ReturnType::fail('Category not found!');
            }

            Category::destroy($id);

            return ReturnType::success('Delete category successfully!');
        } catch (Exception $error) {
            return ReturnType::fail($error);
        }
    }
}

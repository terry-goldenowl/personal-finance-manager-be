<?php

namespace App\Services;

use App\Models\Category;

class CategoryServices
{
    public function create(array $data): ?Category
    {
        if (Category::where(['user_id' => $data['user_id'], 'name' => $data['name']])->exists()) {
            return null;
        }

        $newCategory = Category::create($data);
        return $newCategory;
    }

    public function update($data, int $id): bool
    {
        $category = Category::find($id);
        if (!$category) {
            return false;
        }

        return $category->update($data);
    }

    public function delete($id): bool
    {
        $category = Category::find($id);
        if (!$category) {
            return false;
        }

        return Category::destroy($id);
    }

    public function checkExistsById(int $id): bool
    {
        return Category::where('id', $id)->exists();
    }
}

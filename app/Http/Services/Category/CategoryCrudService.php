<?php

namespace App\Http\Services\Category;

use App\Models\Category;

class CategoryCrudService
{
    /**
     * Create new category
     */
    public function createCategory(array $validated): Category
    {
        return Category::create($validated);
    }

    /**
     * Update category
     */
    public function updateCategory(Category $category, array $validated): Category
    {
        $category->update($validated);
        return $category;
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }
}

<?php

namespace App\Http\Services\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryQueryService
{
    /**
     * List categories with search filter
     */
    public function listCategories(?string $search = null): Collection
    {
        return Category::when($search, function ($q, $search) {
            return $q->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }
}

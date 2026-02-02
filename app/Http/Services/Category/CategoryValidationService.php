<?php

namespace App\Http\Services\Category;

use App\Models\Category;

class CategoryValidationService
{
    /**
     * Validate if category can be deleted (no related tickets)
     */
    public function canDelete(Category $category): array
    {
        if ($category->tickets()->exists()) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus kategori yang memiliki tiket',
                'ticket_count' => $category->tickets()->count()
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate unique category name
     */
    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        $query = Category::where('name', $name);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return !$query->exists();
    }
}

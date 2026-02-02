<?php

namespace App\Http\Services\Department;

use App\Models\Department;

class DepartmentValidationService
{
    /**
     * Validate if department can be deleted (no related users)
     */
    public function canDelete(Department $department): array
    {
        if ($department->users()->exists()) {
            return [
                'valid' => false,
                'message' => 'Tidak dapat menghapus departemen yang memiliki user',
                'user_count' => $department->users()->count()
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate unique department name
     */
    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        $query = Department::where('name', $name);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return !$query->exists();
    }
}

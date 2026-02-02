<?php

namespace App\Http\Services\Department;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

class DepartmentQueryService
{
    /**
     * List departments with search filter
     */
    public function listDepartments(?string $search = null): Collection
    {
        return Department::when($search, function ($q, $search) {
            return $q->where('name', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById(int $id): ?Department
    {
        return Department::find($id);
    }
}

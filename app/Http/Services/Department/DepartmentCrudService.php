<?php

namespace App\Http\Services\Department;

use App\Models\Department;

class DepartmentCrudService
{
    /**
     * Create new department
     */
    public function createDepartment(array $validated): Department
    {
        return Department::create($validated);
    }

    /**
     * Update department
     */
    public function updateDepartment(Department $department, array $validated): Department
    {
        $department->update($validated);
        return $department;
    }

    /**
     * Delete department
     */
    public function deleteDepartment(Department $department): bool
    {
        return $department->delete();
    }
}

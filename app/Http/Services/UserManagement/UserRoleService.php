<?php

namespace App\Http\Services\UserManagement;

class UserRoleService
{
    /**
     * Available roles for user creation
     */
    public function getAvailableRoles(): array
    {
        return ['helpdesk', 'technician', 'supervisor'];
    }

    /**
     * Valid roles for filtering
     */
    public function getValidRoles(): array
    {
        return ['helpdesk', 'technician', 'supervisor', 'requester', 'manager'];
    }

    /**
     * Roles for summary
     */
    public function getSummaryRoles(): array
    {
        return ['helpdesk', 'technician', 'supervisor', 'requester', 'manager', 'master-admin'];
    }
}

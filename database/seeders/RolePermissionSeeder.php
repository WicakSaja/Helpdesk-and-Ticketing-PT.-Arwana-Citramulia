<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // ticket permissions
            'ticket.create',
            'ticket.view.own',
            'ticket.view.all',
            'ticket.comment',
            'ticket.assign',
            'ticket.change_status',
            'ticket.resolve',
            'ticket.close',
            'ticket.escalate',
            'ticket.view.dashboard',
            'ticket.view.report',

            // USER MANAGEMENT
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.assign_role',
            'user.assign_permission',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $requester = Role::firstOrCreate(['name' => 'requester']);
        $helpdesk  = Role::firstOrCreate(['name' => 'helpdesk']);
        $tech      = Role::firstOrCreate(['name' => 'technician']);
        $supervisor= Role::firstOrCreate(['name' => 'supervisor']);
        $manager   = Role::firstOrCreate(['name' => 'manager']);
        $admin     = Role::firstOrCreate(['name' => 'master-admin']);

        $admin->givePermissionTo([
            'user.view',
            'user.create',
            'user.update',
            'user.delete',
            'user.assign_role',
            'user.assign_permission',
        ]);

        $requester->givePermissionTo([
            'ticket.create',
            'ticket.view.own',
            'ticket.comment'
        ]);

        $helpdesk->givePermissionTo([
            'ticket.view.all',
            'ticket.assign',
            'ticket.change_status',
            'ticket.comment',
            'ticket.close'
        ]);

        $tech->givePermissionTo([
            'ticket.view.own',
            'ticket.change_status',
            'ticket.resolve',
            'ticket.comment'
        ]);

        $supervisor->givePermissionTo([
            'ticket.view.all',
            'ticket.assign',
            'ticket.escalate',
            'ticket.change_status',
            'ticket.view.dashboard'
        ]);

        $manager->givePermissionTo([
            'ticket.view.all',
            'ticket.view.dashboard',
            'ticket.view.report'
        ]);
    }
}

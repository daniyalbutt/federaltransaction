<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = Permission::all();
        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo($permission);
        $user = User::where('email', 'info@admin.com')->first();
        $user->assignRole('admin');
    }
}

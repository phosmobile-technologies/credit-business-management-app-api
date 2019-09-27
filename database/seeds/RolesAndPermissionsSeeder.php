<?php

use App\Models\UserPermissions;
use App\Models\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regularUser = Role::create(['name' => UserRoles::REGULAR_USER]);
        $adminUser = Role::create(['name' => UserRoles::ADMIN_USER]);

        $saveMoney = Permission::create(['name' => UserPermissions::SAVE_MONEY]);

        $regularUser->syncPermissions([$saveMoney]);
    }
}

<?php

use App\Models\UserPermissions;
use App\Models\Enums\UserRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @var Role
     */
    private $customer;

    /**
     * @var Role
     */
    private $adminStaff;

    /**
     * @var Role
     */
    private $branchManager;

    /**
     * @var Role
     */
    private $branchAccountant;

    /**
     * @var Role
     */
    private $adminAccountant;

    /**
     * @var
     */
    private $adminManager;

    /**
     * @var
     */
    private $superAdmin;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRoles();

        $this->createCustomerPermissions();
        $this->createAdminStaffPermissions();
        $this->createBranchManagerPermissions();
        $this->createBranchAccountantPermissions();
        $this->createAdminAccountantPermissions();
        $this->createAdminManagerPermissions();
    }


    /**
     * Create the permissions for customers
     */
    private function createCustomerPermissions()
    {
        $this->customer->syncPermissions([
            Permission::create(['name' => UserPermissions::CAN_CREATE_LOAN_APPLICATIONS]),
            Permission::create(['name' => UserPermissions::CAN_CREATE_CONTRIBUTION])
        ]);
    }

    /**
     * Create the permissions for admin staff
     */
    private function createAdminStaffPermissions()
    {
        $this->adminStaff->syncPermissions([
            Permission::firstOrCreate(['name' => UserPermissions::CAN_CREATE_LOANS]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_CONTRIBUTION]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_DELETE_CONTRIBUTION]),
        ]);
    }

    /**
     * Create the permissions for branch Manager
     */
    private function createBranchManagerPermissions()
    {
        $this->branchManager->syncPermissions([
            Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_PROCESS_TRANSACTION]),
        ]);
    }

    /**
     * Create the permissions for admin accountant
     */
    private function createBranchAccountantPermissions()
    {
        $this->branchAccountant->syncPermissions([
            Permission::firstOrCreate(['name' => UserPermissions::CAN_DISBURSE_LOAN]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_INITIATE_LOAN_REPAYMENT]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_INITIATE_CONTRIBUTION_PLAN_TRANSACTION]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_PROCESS_TRANSACTION]),
        ]);
    }

    /**
     * Create the permissions for admin accountant
     */
    private function createAdminAccountantPermissions()
    {
        $this->adminAccountant->syncPermissions([
            Permission::firstOrCreate(['name' => UserPermissions::CAN_DISBURSE_LOAN]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_INITIATE_LOAN_REPAYMENT]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_INITIATE_CONTRIBUTION_PLAN_TRANSACTION]),
            Permission::firstOrCreate(['name' => UserPermissions::CAN_PROCESS_TRANSACTION]),
        ]);
    }

    /**
     * Create the permissions for branch Manager
     */
    private function createAdminManagerPermissions()
    {
        $this->adminManager->syncPermissions([
            Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS])
        ]);
    }

    /**
     * Create all the user roles for the application
     */
    private function createRoles()
    {
        $this->customer = Role::create(['name' => UserRoles::CUSTOMER]);
        $this->adminStaff = Role::create(['name' => UserRoles::ADMIN_STAFF]);
        $this->branchManager = Role::create(['name' => UserRoles::BRANCH_MANAGER]);
        $this->branchAccountant = Role::create(['name' => UserRoles::BRANCH_ACCOUNTANT]);
        $this->adminAccountant = Role::create(['name' => UserRoles::ADMIN_ACCOUNTANT]);
        $this->adminManager = Role::create(['name' => UserRoles::ADMIN_MANAGER]);
        $this->superAdmin = Role::create(['name' => UserRoles::SUPER_ADMIN]);
    }
}

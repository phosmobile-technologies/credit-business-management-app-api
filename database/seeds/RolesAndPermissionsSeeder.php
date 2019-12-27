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
    private $globalManager;

    /**
     * @var Role
     */
    private $adminAccountant;

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
        $this->createGlobalManagerPermissions();
        $this->createAdminAccountantPermissions();
    }

    /**
     * Create the roles and permissions for admin users
     */
    private function createAdminRolesAndPermissions()
    {
//        $adminUser = Role::create(['name' => UserRoles::ADMIN_USER]);
//
//        $canCrudUsers = Permission::create(['name' => UserPermissions::CAN_CRUD_USERS]);
//
//        $adminUser->syncPermissions([$canCrudUsers]);
    }

    /**
     * Create the permissions for customers
     */
    private function createCustomerPermissions()
    {
        $createLoanApplication = Permission::create(['name' => UserPermissions::CAN_CREATE_LOAN_APPLICATIONS]);

        $this->customer->syncPermissions([$createLoanApplication]);
    }

    /**
     * Create the permissions for admin staff
     */
    private function createAdminStaffPermissions()
    {
        $createLoan = Permission::create(['name' => UserPermissions::CAN_CREATE_LOANS]);

        $this->adminStaff->syncPermissions([$createLoan]);
    }

    /**
     * Create the permissions for branch Manager
     */
    private function createBranchManagerPermissions()
    {
        $updateLoanStatus = Permission::create(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS]);

        $this->branchManager->syncPermissions([$updateLoanStatus]);
    }

    /**
     * Create the permissions for global Manager
     */
    private function createGlobalManagerPermissions()
    {
        $updateLoanStatus = Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS]);

        $this->globalManager->syncPermissions([$updateLoanStatus]);
    }

    /**
     * Create the permissions for admin accountant
     */
    private function createAdminAccountantPermissions()
    {
        $canDisburseLoan = Permission::firstOrCreate(['name' => UserPermissions::CAN_DISBURSE_LOAN]);

        $this->adminAccountant->syncPermissions([$canDisburseLoan]);
    }

    /**
     * Create all the user roles for the application
     */
    private function createRoles() {
        $this->customer = Role::create(['name' => UserRoles::CUSTOMER]);
        $this->adminStaff = Role::create(['name' => UserRoles::ADMIN_STAFF]);
        $this->branchManager = Role::create(['name' => UserRoles::BRANCH_MANAGER]);
        $this->globalManager = Role::create(['name' => UserRoles::GLOBAL_MANAGER]);
        $this->adminAccountant = Role::create(['name' => UserRoles::ADMIN_ACCOUNTANT]);
        $adminManager = Role::create(['name' => UserRoles::ADMIN_MANAGER]);
        $superAdmin= Role::create(['name' => UserRoles::SUPER_ADMIN]);
    }
}

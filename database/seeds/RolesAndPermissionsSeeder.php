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
        $createLoanApplication = Permission::create(['name' => UserPermissions::CAN_CREATE_LOAN_APPLICATIONS]);
        $createContribution = Permission::create(['name' => UserPermissions::CAN_CREATE_CONTRIBUTION]);

        $this->customer->syncPermissions([$createLoanApplication, $createContribution]);
    }

    /**
     * Create the permissions for admin staff
     */
    private function createAdminStaffPermissions()
    {
        $createLoan = Permission::firstOrCreate(['name' => UserPermissions::CAN_CREATE_LOANS]);
        $updateContribution = Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_CONTRIBUTION]);
        $deleteContribution = Permission::firstOrCreate(['name' => UserPermissions::CAN_DELETE_CONTRIBUTION]);
        $initiateLoanRepayment = Permission::firstOrCreate(['name' => UserPermissions::CAN_INITIATE_LOAN_REPAYMENT]);

        $this->adminStaff->syncPermissions([$createLoan, $updateContribution, $deleteContribution, $initiateLoanRepayment]);
    }

    /**
     * Create the permissions for branch Manager
     */
    private function createBranchManagerPermissions()
    {
        $updateLoanStatus = Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS]);

        $this->branchManager->syncPermissions([$updateLoanStatus]);
    }

    /**
     * Create the permissions for admin accountant
     */
    private function createBranchAccountantPermissions()
    {
        $canDisburseLoan = Permission::firstOrCreate(['name' => UserPermissions::CAN_DISBURSE_LOAN]);

        $this->branchAccountant->syncPermissions([$canDisburseLoan]);
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
     * Create the permissions for branch Manager
     */
    private function createAdminManagerPermissions()
    {
        $updateLoanStatus = Permission::firstOrCreate(['name' => UserPermissions::CAN_UPDATE_LOAN_STATUS]);

        $this->adminManager->syncPermissions([$updateLoanStatus]);
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

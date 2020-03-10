<?php

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\UserProfile;
use App\User;
use App\Models\Enums\UserRoles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedCustomers();
        $this->seedAdminStaff();
        $this->seedBranchManager();
        $this->seedBranchAccountant();
        $this->seedAdminAccountant();
        $this->seedAdminManager();
        $this->seedSuperAdmin();
    }

    /**
     * Seed admin users
     */
    private function seedSuperAdmin()
    {
        $faker = Faker::create();

        // Create the Admin user and assign the admin role
        $adminUser = User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'phone_number' => $faker->phoneNumber,
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        $adminUser->assignRole(UserRoles::SUPER_ADMIN);
    }

    /**
     * Seed Customers
     */
    private function seedCustomers() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'user',
                'last_name' => "{$index}",
                'email' => "user{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::CUSTOMER);
        }
    }

    /**
     * Seed Admin Staff
     */
    private function seedAdminStaff() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'adminStaff',
                'last_name' => "{$index}",
                'email' => "adminStaff{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::ADMIN_STAFF);
        }
    }

    /**
     * Seed Branch Manager
     */
    private function seedBranchManager() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'branchManager',
                'last_name' => "{$index}",
                'email' => "branchManager{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::BRANCH_MANAGER);
        }
    }

    /**
     * Seed Branch Accountant
     */
    private function seedBranchAccountant() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'branchAccountant',
                'last_name' => "{$index}",
                'email' => "branchAccountant{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::BRANCH_ACCOUNTANT);
        }
    }

    /**
     * Seed Admin Accountant
     */
    private function seedAdminAccountant() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'adminAccountant',
                'last_name' => "{$index}",
                'email' => "adminAccountant{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::ADMIN_ACCOUNTANT);
        }
    }

    /**
     * Seed Admin Manager
     */
    private function seedAdminManager() {
        foreach (range(1, 5) as $index) {
            $user = factory(User::class)->create([
                'first_name' => 'adminManager',
                'last_name' => "{$index}",
                'email' => "adminManager{$index}@users.com",
            ]);

            $company = Company::first();
            $branch = CompanyBranch::inRandomOrder()->first();
            $userProfile = factory(UserProfile::class)->make([
                'company_id' => $company->id,
                'branch_id' => $branch->id
            ]);

            $user->profile()->save($userProfile);
            $user->user_profile_id = $user->profile->id;
            $user->save();
            $user->assignRole(UserRoles::ADMIN_MANAGER);
        }
    }
}

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
        $this->seedRegularUsers();
        $this->seedAdminUser();
    }

    /**
     * Seed admin users
     */
    private function seedAdminUser()
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
     * Seed regular users
     */
    private function seedRegularUsers() {
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
}

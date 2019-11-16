<?php

use App\Models\UserProfile;
use App\User;
use App\Models\UserRoles;
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
        $adminUser->assignRole(UserRoles::ADMIN_USER);
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

            $user->profile()->save(factory(UserProfile::class)->make());
            $user->assignRole(UserRoles::REGULAR_USER);
        }
    }
}

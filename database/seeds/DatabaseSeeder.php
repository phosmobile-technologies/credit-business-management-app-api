<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CompanyAndBranchSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LoanTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(ContributionPlanTableSeeder::class);
    }
}

<?php

use App\Models\Company;
use App\Models\CompanyBranch;
use Illuminate\Database\Seeder;

class CompanyAndBranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = factory(Company::class)->create([
            'name' => "Springverse"
        ]);

        $branches = factory(CompanyBranch::class, 5)->make();

        $company->branches()->saveMany($branches);
    }
}

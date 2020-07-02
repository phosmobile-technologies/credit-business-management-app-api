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
            'name' => "UMC"
        ]);

        $branches = [
            [
                'company_id' => $company->id,
                'name'       => 'UMC EGBEDA',
                'location'   => 'Block A Suite 12 PrimalTek Plaza Egbeda, Opp Gowon Estate Along Egbeda Iyana-Ipaja Road Egbeda'
            ],
            [
                'company_id' => $company->id,
                'name'       => 'UMC AJAH',
                'location'   => 'Shop 10, Deegov Plaza Off Cele Bus-Stop, Badore Road, Ajah Lagos'
            ],
            [
                'company_id' => $company->id,
                'name'       => 'UMC IKEJA',
                'location'   => 'Suite 15-16, Royal Samodun Shopping Arcade, New Alade Market, Off Allen Avenue, Ikeja Lagos'
            ]
        ];

        foreach ($branches as $branch) {
            CompanyBranch::create($branch);
        }

    }
}

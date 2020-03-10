<?php

use App\Models\Company;
use App\Models\ContributionPlan;
use App\User;
use Illuminate\Database\Seeder;

class ContributionPlanTableSeeder extends Seeder
{
    /**
     * @var \App\User
     */
    private $users;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $springVerse = Company::first();

        $this->users = (User::whereHas('profile')->with('profile')->get())->filter(function ($user) use ($springVerse) {
            return $user->profile->company_id === $springVerse->id;
        });

        $this->users->each(function ($user) {
            $user->contributionPlans()->createMany(
                factory(ContributionPlan::class, 2)->make()->toArray()
            );
        });
    }
}

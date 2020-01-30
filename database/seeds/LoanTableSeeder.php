<?php

use App\Models\Company;
use App\Models\Loan;
use App\User;
use Illuminate\Database\Seeder;

class LoanTableSeeder extends Seeder
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

        $this->seedDisbursedLoans();
        $this->seedNotDisbursedLoans();
        $this->seedDefaultingLoans();
        $this->seedNoneDefaultingLoans();
    }

    /**
     * Seed disbursed loans into the database
     */
    private function seedDisbursedLoans()
    {
        $this->users->each(function ($user) {
            $user->loans()->createMany(
                factory(Loan::class, 2)->state('disbursed_loan')->make()->toArray()
            );
        });
    }

    /**
     * Seed not disbursed loans into the database
     */
    private function seedNotDisbursedLoans() {
        $this->users->each(function ($user) {
            $user->loans()->createMany(
                factory(Loan::class, 2)->state('not_disbursed_loan')->make()->toArray()
            );
        });
    }

    /**
     * Seed defaulting loans into the database
     */
    private function seedDefaultingLoans() {
        $this->users->each(function ($user) {
            $user->loans()->createMany(
                factory(Loan::class, 2)->state('defaulting_loan')->make()->toArray()
            );
        });
    }

    /**
     * Seed none defaulting loans into the database
     */
    private function seedNoneDefaultingLoans() {
        $this->users->each(function ($user) {
            $user->loans()->createMany(
                factory(Loan::class, 2)->state('non_defaulting_loan')->make()->toArray()
            );
        });
    }
}

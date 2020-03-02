<?php

use App\Models\Company;
use App\Models\Wallet;
use App\User;
use Illuminate\Database\Seeder;

class WalletTableSeeder extends Seeder
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
            $user->wallet()->createMany(
                factory(Wallet::class, 2)->make()->toArray()
            );
        });
    }
}

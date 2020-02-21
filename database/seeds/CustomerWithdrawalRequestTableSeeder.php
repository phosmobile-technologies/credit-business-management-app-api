<?php

use App\Models\Company;
use App\Models\enums\RequestStatus;
use App\Models\enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use App\User;
use Illuminate\Database\Seeder;

class CustomerWithdrawalRequestTableSeeder extends Seeder
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
            return $user->profile->user_id === $springVerse->id;
        });

        $this->users->each(function ($user) {
            $user->customerwithdrawalrequests()->createMany(
                factory(CustomerWithdrawalRequest::class, 2)->make()->toArray()
            );
        });
        $this->seedCustomerWithdrawalRequests();
    }

    private function seedCustomerWithdrawalRequests()
    {
        $customerwithdrawalrequests = customerwithdrawalrequest::limit(5)->get();

        foreach ($customerwithdrawalrequests as $customerwithdrawalrequest) {
            $RequestTypes = [
                RequestType::BRANCH_FUND,
                RequestType::BRANCH_EXTRA_FUND,
                RequestType::DEFAULT_CANCELLATION,
                RequestType::VENDOR_PAYOUT,
                RequestType::CONTRIBUTION_WITHDRAWAL
            ];

            $RequestStatuses = [
                RequestStatus::APPROVED_BY_BRANCH_MANAGER,
                RequestStatus::DISAPPROVED_BY_BRANCH_MANAGER,
                RequestStatus::DISAPPROVED_BY_GLOBAL_MANAGER,
                RequestStatus::APPROVED_BY_GLOBAL_MANAGER,
                RequestStatus::PENDING,
                RequestStatus::DISBURSED,
            ];

            factory(CustomerWithdrawalRequest::class, 2)->create([
                'request_type' => $RequestTypes[array_rand($RequestTypes)],
                'user_id' => $customerwithdrawalrequest->id,
                'request_status' => $RequestStatuses[array_rand($RequestStatuses)]
            ]);
        }
    }
}

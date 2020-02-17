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
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
                RequestType::VENDOR_PAYOUTT
            ];

            $RequestStatuses = [
                RequestStatus::APPROVED,
                RequestStatus::DECLINED,
                RequestStatus::PENDING,
                RequestStatus::DISBURSED,
            ];

            factory(CustomerWithdrawalRequest::class, 2)->create([
                'Request_type' => $RequestTypes[array_rand($RequestTypes)],
                'owner_id' => $customerwithdrawalrequest->id,
                'Request_status' => $RequestStatuses[array_rand($RequestStatuses)]
            ]);
        }
    }
}

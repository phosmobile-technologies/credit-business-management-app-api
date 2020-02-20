<?php

namespace Tests\GraphQL\Helpers\Traits;


use App\Models\Enums\RequestStatus;
use App\Models\CustomerWithdrawalRequest;

trait InteractsWithTestCustomerWithdrawalRequests
{
    use InteractsWithTestUsers;

    /**
     * Create a test CustomerWithdrawalRequest.
     *
     * @param null $user
     * @param null $numberOfCustomerWithdrawalRequests
     * @return mixed
     */
    public function createTestCustomerWithdrawalRequest($user = null, $userRoles=null, $numberOfCustomerWithdr

        if (!$user) {
            $user = $this->createUser();
        }

        if (!$numberOfCustomerWithdrawalRequests) {
            return factory(CustomerWithdrawalRequest::class)->create([
                'request_status' => RequestStatus::PENDING(),
                'user_id' => $user->id
            ]);
        }

        return factory(CustomerWithdrawalRequest::class, $numberOfCustomerWithdrawalRequests)->create([
            'request_status' => RequestStatus::PENDING(),
            'user_id' => $user->id
        ]);
    }
}

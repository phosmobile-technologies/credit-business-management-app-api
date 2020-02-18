<?php

namespace App\Repositories;


use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\RequestStatus;
use App\Models\Enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use App\Repositories\Interfaces\CustomerWithdrawalRequestRepositoryInterface;

class CustomerWithdrawalRequestRepository implements CustomerWithdrawalRequestRepositoryInterface
{

    /**
     * Insert a CustomerWithdrawalRequest in the database
     *
     * @param array $customerwithdrawalrequestData
     * @return CustomerWithdrawalRequest
     */
    public function create(array $customerwithdrawalrequestData): CustomerWithdrawalRequest
    {
        return CustomerWithdrawalRequest::create($customerwithdrawalrequestData);
    }

    /**
     * Update a CustomerWithdrawalRequest in the database.
     *
     * @param string $id
     * @param array $customerwithdrawalrequestData
     * @return CustomerWithdrawalRequest
     */
    public function update(string $id, array $customerwithdrawalrequestData): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest = $this->find($id);

        $customerwithdrawalrequest->update($customerwithdrawalrequestData);
        return $customerwithdrawalrequest;
    }

    /**
     * Find a CustomerWithdrawalRequest by id.
     *
     * @param string $user_id
     * @return CustomerWithdrawalRequest|null
     */
    public function find(string $user_id): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest = CustomerWithdrawalRequest::findOrFail($user_id);
        return $customerwithdrawalrequest;
    }

    /**
     * Store a customer withdrawal request disbursement action in the database.
     *
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @param float $requestAmount
     * @return CustomerWithdrawalRequest
     */
    public function disburseCustomerWithdrawalRequest(CustomerWithdrawalRequest $customerwithdrawalrequest, float $requestAmount): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest->request_status = RequestStatus::DISBURSED;
        $customerwithdrawalrequest->request_amount = $customerwithdrawalrequest->$requestAmount;
        $customerwithdrawalrequest->save();

        return $customerwithdrawalrequest;
    }

    /**
     * Update the request_state of a CustomerWithdrawalRequest in the database.
     *
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @param string $requestState
     * @return CustomerWithdrawalRequest
     */
    public function updateRequestState(CustomerWithdrawalRequest $customerwithdrawalrequest, string $requestState): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest->request_status = $requestState;
        $customerwithdrawalrequest->save();

        return $customerwithdrawalrequest;

    }

    /**
     * Update the request_type of a CustomerWithdrawalRequest in the database.
     *
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @param string $requestType
     * @return CustomerWithdrawalRequest
     */
    public function updateRequestType(CustomerWithdrawalRequest $customerwithdrawalrequest, string $requestType): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest->request_type = $requestType;
        $customerwithdrawalrequest->save();

        return $customerwithdrawalrequest;
    }
}

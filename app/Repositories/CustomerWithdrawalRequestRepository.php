<?php

namespace App\Repositories;


use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\DisbursementStatus;
use App\Models\Enums\RequestStatus;
use App\Models\Enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use App\Repositories\Interfaces\CustomerWithdrawalRequestRepositoryInterface;

class CustomerWithdrawalRequestRepository implements CustomerWithdrawalRequestRepositoryInterface
{

    /**
     * Insert a CustomerWithdrawalRequest in the database
     *
     * @param array $CustomerWithdrawalRequestData
     * @return CustomerWithdrawalRequest
     */
    public function create(array $CustomerWithdrawalRequestData): CustomerWithdrawalRequest
    {
        return CustomerWithdrawalRequest::create($CustomerWithdrawalRequestData);
    }

    /**
     * Update a CustomerWithdrawalRequest in the database.
     *
     * @param string $id
     * @param array $CustomerWithdrawalRequestData
     * @return CustomerWithdrawalRequest
     */
    public function update(string $id, array $CustomerWithdrawalRequestData): CustomerWithdrawalRequest
    {
        $CustomerWithdrawalRequest = $this->find($id);

        $CustomerWithdrawalRequest->update($CustomerWithdrawalRequestData);
        return $CustomerWithdrawalRequest;
    }

    /**
     * Find a CustomerWithdrawalRequest by id.
     *
     * @param string $user_id
     * @return CustomerWithdrawalRequest|null
     */
    public function find(string $user_id): CustomerWithdrawalRequest
    {
        $CustomerWithdrawalRequest = CustomerWithdrawalRequest::findOrFail($user_id);
        return $CustomerWithdrawalRequest;
    }

    /**
     * Update the request_state of a CustomerWithdrawalRequest in the database.
     *
     * @param CustomerWithdrawalRequest $CustomerWithdrawalRequest
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
     * @param CustomerWithdrawalRequest $CustomerWithdrawalRequest
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

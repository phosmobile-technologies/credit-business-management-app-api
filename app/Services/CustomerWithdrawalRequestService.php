<?php

namespace App\Services;


use App\Events\RequestStatusChanged;
use App\Events\RequestDisbursed;
use App\Events\CustomerWithdrawalRequestApprovedByBranchManager;
use App\Events\CustomerWithdrawalRequestApprovedByGlobalManager;
use App\Events\CustomerWithdrawalRequestDisApprovedByGlobalManager;
use App\Events\CustomerWithdrawalRequestDisApprovedByBranchManager;
use App\Events\NewCustomerWithdrawalRequestCreated;
use App\GraphQL\Errors\GraphqlError;
use App\Models\Enums\RequestStatus;
use App\Models\Enums\RequestType;
use App\Models\CustomerWithdrawalRequest;
use App\Repositories\Interfaces\CustomerWithdrawalRequestRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CustomerWithdrawalRequestService
{
    /**
     * @var CustomerWithdrawalRequestRepositoryInterface
     */
    private $customerwithdrawalrequestRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * CustomerWithdrawalRequestService constructor.
     *
     * @param CustomerWithdrawalRequestRepositoryInterface $customerwithdrawalrequestRepository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(CustomerWithdrawalRequestRepositoryInterface $customerwithdrawalrequestRepository, UserRepositoryInterface $userRepository)
    {
        $this->CustomerWithdrawalRequestRepository = $customerwithdrawalrequestRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new Request.
     *
     * @param array $customerwithdrawalrequeststatusData
     * @returnCustomerWithdrawalRequest
     * @throws GraphqlError
     */
    public function create(array $customerwithdrawalrequestData):CustomerWithdrawalRequest
    {
        // Check to ensure that a user can only have one activeCustomerWithdrawalRequest at a time
        $user = $this->userRepository->find($customerwithdrawalrequestData['user_id']);
        if (count($user->activeCustomerWithdrawalRequests()) > 0) {
            throw new GraphqlError('This user already has an active CustomerWithdrawalRequest and cannot take a new CustomerWithdrawalRequest');
        }

        $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->create($customerwithdrawalrequestData);

        event(new NewCustomerWithdrawalRequestCreated($customerwithdrawalrequest));

        return $customerwithdrawalrequest;
    }

    /**
     * Update the request_state of a CustomerWithdrawalRequest.
     *
     * @param string $customerwithdrawalrequestID
     * @param string $RequestStatus
     * @param null|string $message
     * @return CustomerWithdrawalRequest
     */
    public function updateRequestStatus(string $customerwithdrawalrequestID, string $requestStatus, ?string $message)
    {
        $customerwithdrawalrequest = $this->CustomerWithdrawalRequestRepository->find($customerwithdrawalrequestID);
        $oldRequestStatus = $customerwithdrawalrequest->request_status;

        $this->customerwithdrawalrequestRepository->updateRequestState($customerwithdrawalrequest, $requestStatus);

        event(new RequestStatusChanged($customerwithdrawalrequest, $oldRequestStatus, Auth::user(), $message));
        return $customerwithdrawalrequest;
    }

    /**
     * Update a withdrawal request
     *
     * @param array $customerwithdrawalrequest
     * @return CustomerWithdrawalRequest
     */
    public function update(array $customerwithdrawalrequest): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest = collect($customerwithdrawalrequest);
        $id = $customerwithdrawalrequest['id'];
        $data = $customerwithdrawalrequest->except(['id'])->toArray();

        return $this->customerwithdrawalrequestRepository->update($id, $data);
    }

    /**
     * Disburse a request
     *
     * @param string $customerwithdrawalrequestID
     * @param float $requestAmount
     * @param null|string $message
     * @return
     * @throws \Exception
     */
    public function disburseCustomerWithdrawalRequest(string $customerwithdrawalrequestID, float $requestAmount, ?string $message)
    {
        $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->find($customerwithdrawalrequestID);
        $requestAmount = $customerwithdrawalrequest->request_amount;

        if ($customerwithdrawalrequest->request_status !== RequestStatus::APPROVED_BY_GLOBAL_MANAGER()->getValue()) {
            throw new GraphqlError("Cannot disburse funds for a withdrawal request that is not approved");
        }

        if ($customerwithdrawalrequest->request_status !== RequestStatus::APPROVED_BY_BRANCH_MANAGER()->getValue()) {
            throw new GraphqlError("Cannot disburse funds for a withdrawal request that is not approved");
        }

        $this->customerwithdrawalrequestRepository->disburseCustomerWithdrawalRequest($customerwithdrawalrequest, $requestAmount);

        event(new RequestDisbursed($customerwithdrawalrequest, $requestAmount, $message));

        return $customerwithdrawalrequest;

    }
}

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
use App\User;
use Illuminate\Support\Facades\DB;

class CustomerWithdrawalRequestService
{
    /**
     * @var CustomerWithdrawalRequestRepositoryInterface
     */
    private $customerwithdrawalrequestRepository;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * CustomerWithdrawalRequestService constructor.
     *
     * @param CustomerWithdrawalRequestRepositoryInterface $customerwithdrawalrequestRepository
     * @param TransactionService $transactionService
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(CustomerWithdrawalRequestRepositoryInterface $customerwithdrawalrequestRepository, TransactionService $transactionService, UserRepositoryInterface $userRepository)
    {
        $this->customerwithdrawalrequestRepository = $customerwithdrawalrequestRepository;
        $this->transactionService = $transactionService;
        $this->userRepository = $userRepository;
    }

    public function initiateCustomerWithdrawalRequest(string $owner_id, array $transactionDetails)
    {
        switch ($transactionDetails['request_type']) {
            case RequestType::BRANCH_FUND:
                return $this->initiateBranchFundPayentRequest($owner_id, $customerwithdrawalrequestDetails);
                break;

            case RequestType::BRANCH_EXTRA_FUND:
                return $this->initiateBranchExtraFundPaymentRequest($owner_id, $customerwithdrawalrequestDetails);
                break;

            case RequestType::DEFAULT_CANCELLATION:
                return $this->initiateDefaultCancellationPaymentRequest($owner_id, $customerwithdrawalrequestDetails);
                break;

            case RequestType::VENDOR_PAYOUT:
                return $this->initiateVendorPayoutPaymentRequest($owner_id, $customerwithdrawalrequestDetails);
                break;

            case RequestType::CONTRIBUTION_WITHDRAWAL:
                return $this->initiateContributionWithdrawalPaymentRequest($owner_id, $customerwithdrawalrequestDetails);
                break;
        }
    }


    /**
     * Process (approve or disapprove) a customer withdrawal request.
     *
     * @param User $user
     * @param string $customerwithdrawalrequest_id
     * @param string $type
     * @param null|string $message
     * @return CustomerWithdrawalRequest
     */
    public function processCustomerWithdrawalRequest(User $user, string $user_id, string $type, ?string $message): CustomerWithdrawalRequest
    {
        $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->find($user_id);

        switch ($customerwithdrawalrequest->request_type) {
            case (RequestType::BRANCH_FUND):
                return $this->processBranchFundPayentRequest($user, $customerwithdrawalrequest, $type, $message);
                break;

            case (RequestType::BRANCH_EXTRA_FUND):
                return $this->processBranchExtraFundPaymentRequest($user, $customerwithdrawalrequest, $type, $message);
                break;

            case (RequestType::DEFAULT_CANCELLATION):
                return $this->processDefaultCancellationPaymentRequest($user, $customerwithdrawalrequest, $type, $message);
                break;

                case (RequestType::VENDOR_PAYOUT):
                return $this->processVendorPayoutPaymentRequest($user, $customerwithdrawalrequest, $type, $message);
                break;

            case (RequestType::CONTRIBUTION_WITHDRAWAL):
                return $this->processContributionWithdrawalPaymentRequest($user, $customerwithdrawalrequest, $type, $message);
                break;
        }
    }

    /**
     * Initiate a Branch Fund payment requests.
     *
     * @param string $customer_withdrawal_request_id
     * @param array $transactionDetails
     * @return CustomerWithdrawalRequest
     */
    public function initiateBranchFundPayentRequest(string $customer_withdrawal_request_id, array $customerwithdrawalrequestDetails): CustomerWithdrawalRequest
    {
        return $this->createCustomerWithdrawalRequest(RequestType::BRANCH_FUND, $customer_withdrawal_request_id, $customerwithdrawalrequestDetails);
    }

    /**
     * Initiate a BranchExtraFund payment requests.
     *
     * @param string $customer_withdrawal_request_id
     * @param array $transactionDetails
     * @return CustomerWithdrawalRequest
     */
    public function initiateBranchExtraFundPayentRequest(string $customer_withdrawal_request_id, array $customerwithdrawalrequestDetails): CustomerWithdrawalRequest
    {
        return $this->createCustomerWithdrawalRequest(RequestType::BRANCH_EXTRA_FUND, $customer_withdrawal_request_id, $customerwithdrawalrequestDetails);
    }

    /**
     * Initiate a DefaultCancellation payment requests.
     *
     * @param string $customer_withdrawal_request_id
     * @param array $customerwithdrawalrequestDetails
     * @return CustomerWithdrawalRequest
     */
    public function initiateDefaultCancellationPayentRequest(string $customer_withdrawal_request_id, array $customerwithdrawalrequestDetails): CustomerWithdrawalRequest
    {
        return $this->createCustomerWithdrawalRequest(RequestType::DEFAULT_CANCELLATION, $customer_withdrawal_request_id, $customerwithdrawalrequestDetails);
    }

    /**
     * Initiate a VendorPayout payment requests.
     *
     * @param string $customer_withdrawal_request_id
     * @param array $customerwithdrawalrequestDetails
     * @return CustomerWithdrawalRequest
     */
    public function initiateVendorPayoutPayentRequest(string $customer_withdrawal_request_id, array $customerwithdrawalrequestDetails): CustomerWithdrawalRequest
    {
        return $this->createCustomerWithdrawalRequest(RequestType::VENDOR_PAYOUT, $customer_withdrawal_request_id, $customerwithdrawalrequestDetails);
    }

    /**
     * Initiate a ContributionWithdrawal payment requests.
     *
     * @param string $customer_withdrawal_request_id
     * @param array $customerwithdrawalrequestDetails
     * @return CustomerWithdrawalRequest
     */
    public function initiateContributionWithdrawalPayentRequest(string $customer_withdrawal_request_id, array $customerwithdrawalrequestDetails): CustomerWithdrawalRequest
    {
        return $this->createCustomerWithdrawalRequest(RequestType::CONTRIBUTION_WITHDRAWAL, $customer_withdrawal_request_id, $customerwithdrawalrequestDetails);
    }

    /**
     * Create a new CustomerWithdrawalRequest.
     *
     * @param string $RequestType
     * @param string $ownerId
     * @param array $customerwithdrawalrequestDetails
     * @return \App\Models\CustomerWithdrawalRequest
     */
    private function createCustomerWithdrawalRequest(string $RequestType, string $ownerId, array $customerwithdrawalDetails)
    {
        $createCustomerWithdrawalData = [
            'request_id' => $ownerId,
            'request_type' => $RequestType,
            'request_date' => $customerwithdrawalDetails['request_date'],
            'request_type' => $customerwithdrawalDetails['request_type'],
            'request_amount' => $customerwithdrawalDetails['request_amount'],
            'request_status' => RequestStatus::PENDING
        ];

        $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->create($customerwithdrawalrequestData);

        return $customerwithdrawalrequest;
    }

    /**
     * Create a new Request.
     *
     * @param array $customerwithdrawalrequestData
     * @return CustomerWithdrawalRequest
     * @throws GraphqlError
     */
    public function create(array $customerwithdrawalrequestData):CustomerWithdrawalRequest
    {
        // Check to ensure that a user can only have one activeCustomerWithdrawalRequest at a time
        $user = $this->userRepository->find($customerwithdrawalrequestData['user_id']);
        if (count($user->activeCustomerWithdrawalRequests()) > 0) {
            throw new GraphqlError('This user already has an active CustomerWithdrawalRequest and cannot take a new CustomerWithdrawalRequest');
        }

        // Ensure that the default values when creating a request are set
        $customerwithdrawalrequestData['request_status'] = RequestStatus::PENDING;
        $customerwithdrawalrequestData['request_balance'] = null;

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

    /**
     * Process (approve or disapprove) a branch fund payment request.
     *
     * @param User $user
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @param string $type
     * @param null|string $message
     * @return CustomerWithdrawalRequest
     */
    private function processBranchFundPayentRequest(User $user, CustomerWithdrawalRequest $customerwithdrawalrequest, string $type, ?string $message)
    {
        DB::customerwithdrawalrequest(function () use ($customerwithdrawalrequest, $type, $user, $message) {
            switch ($type) {
                case RequestStatus::APPROVED_BY_BRANCH_MANAGER:
                    $contributionPlan = $this->contributionRepository->find($customerwithdrawalrequest->owner_id);
                    $this->customerwithdrawalrequestRepository->addPayment($contributionPlan, $customerwithdrawalrequest);
                    $this->customerwithdrawalrequestRepository->updateRequestStatus($customerwithdrawalrequest, RequestStatus::APPROVED_BY_BRANCH_MANAGER);
                    break;

                case RequestStatus::DISAPPROVED_BY_BRANCH_MANAGER:
                    $this->transactionRepository->updateRequestStatus($customerwithdrawalrequest, RequestStatus::DISAPPROVED_BY_BRANCH_MANAGER);
                    break;

                case RequestStatus::APPROVED_BY_GLOBAL_MANAGER:
                    $contributionPlan = $this->contributionRepository->find($customerwithdrawalrequest->owner_id);
                    $this->customerwithdrawalrequestRepository->addPayment($contributionPlan, $customerwithdrawalrequest);
                    $this->transactionRepository->updateRequestStatus($customerwithdrawalrequest, RequestStatus::APPROVED_BY_GLOBAL_MANAGER);
                    break;

                case RequestStatus::DISAPPROVED_BY_GLOBAL_MANAGER:
                    $this->transactionRepository->updateRequestStatus($customerwithdrawalrequest, RequestStatus::DISAPPROVED_BY_GLOBAL_MANAGER);
                    break;
            }

            $this->customerwithdrawalrequestRepository->storeRequestStatus($customerwithdrawalrequest, $user->id, $type, $message);
        });

        return $customerwithdrawalrequest;
    }

    /**
     * Process (approve or disapprove) a contribution withdrawal transaction.
     *
     * @param User $user
     * @param Transaction $transaction
     * @param string $type
     * @param null|string $message
     * @return Transaction
     */
    private function processBranchExtraFundPaymentRequest(User $user, Transaction $transaction, string $type, ?string $message)
    {
        DB::transaction(function () use ($transaction, $type, $user, $message) {
            switch ($type) {
                case TransactionProcessingActions::APPROVE:
                    $contributionPlan = $this->contributionRepository->find($transaction->owner_id);
                    $this->contributionRepository->withdraw($contributionPlan, $transaction);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $type, $message);
        });

        return $transaction;
    }

    /**
     * @param User $user The user processing the transaction
     * @param Transaction $transaction
     * @param string $type
     * @param null|string $message
     * @return Transaction|null
     */
    private function processcustomerwithdrawalrequestRepaymentTransaction(User $user, Transaction $transaction, string $type, ?string $message)
    {
        DB::transaction(function () use ($transaction, $type, $user, $message) {
            switch ($type) {
                case TransactionProcessingActions::APPROVE:
                    $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->find($transaction->owner_id);
                    $this->customerwithdrawalrequestRepository->repaycustomerwithdrawalrequest($customerwithdrawalrequest, $transaction->request);
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::COMPLETED);
                    break;

                case TransactionProcessingActions::DISAPPROVE:
                    $this->transactionRepository->updateTransactionStatus($transaction, TransactionStatus::FAILED);
                    break;
            }

            $this->transactionRepository->storeProcessedTransaction($transaction, $user->id, $type, $message);
        });

        return $transaction;
    }

    /**
     * Repay a customerwithdrawalrequest
     *
     * @param string $customerwithdrawalrequest_id
     * @param array $transactionDetails
     * @return \App\Models\Transaction
     * @throws GraphqlError
     */
    public function initiateCustomerWithdrawalRequestRepayment(string $customerwithdrawalrequest_id, array $transactionDetails)
    {
        $customerwithdrawalrequest = $this->customerwithdrawalrequestRepository->find($customerwithdrawalrequest_id);

        $requestAmount = $transactionDetails['request'];

        if ($transactionDetails['request'] > $customerwithdrawalrequest->customerwithdrawalrequest_balance) {
            throw new GraphqlError("Transaction amount {$requestAmount} is greater than the total customerwithdrawalrequest balance");
        }

        if ($transactionDetails['request_type'] !== RequestType::customerwithdrawalrequest_REPAYMENT) {
            throw new GraphqlError("The transaction type selected must be customerwithdrawalrequest Repayment");
        }

        $transaction = $this->transactionService->initiateCustomerWithdrawalRequestRepayment($customerwithdrawalrequest, $transactionDetails);

        return $transaction;
    }
}

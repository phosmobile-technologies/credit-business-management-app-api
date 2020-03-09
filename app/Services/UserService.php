<?php

namespace App\Services;


use App\Events\NewUserRegistered;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\UserRegistrationSource;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;

    /**
     * @var WalletRepositoryInterface
     */
    private $walletRepository;

    private  $defaultPassword;

    public function __construct(UserRepositoryInterface $userRepository, UserProfileRepositoryInterface $userProfileRepository, WalletRepositoryInterface $walletRepository)
    {
        $this->userRepository = $userRepository;
        $this->userProfileRepository = $userProfileRepository;
        $this->walletRepository = $walletRepository;
    }

    /**
     * Register a new user.
     *
     * @param $attributes
     * @return array
     */
    public function registerUser(array $attributes): array
    {
        $attributes = collect($attributes);

        $roles = $attributes['roles'];
        $source = $attributes['source'];

        $userData = $attributes->only([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'password'
        ])->toArray();

        // Generate a random password for the user registration via backend
        if(UserRegistrationSource::BACKEND){
            $this->defaultPassword = Str::random(8);
            $userData['password'] = $this->defaultPassword;
        }else{
            // mailing event requires defaultPassword not to be null
            $this->defaultPassword = $userData['password'];
        }

        // TODO: Figure why the 'directive' index is added by Lighthouse-php to the args it passes down
        $userProfileData = $attributes->except([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'roles',
            'directive',
            'password'
        ])->toArray();
        $userProfileData['customer_identifier'] = $this->generateCustomerIdentifier();

        $user = $this->userRepository->createUser($userData);
        $this->userRepository->attachUserProfile($user, $userProfileData);
        $this->userRepository->attachUserRoles($user, $roles, $source);

        event(new NewUserRegistered($user, $this->defaultPassword, $source));

        return [
            "user" => $user
        ];
    }

    /**
     * Generate a random custom identifier for a user.
     *
     * @return int
     */
    public function generateCustomerIdentifier(): int
    {
        $identifier = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the customer_identifier exists already
        if ($this->userProfileRepository->customerIdentifierExists($identifier)) {
            return self::generateCustomerIdentifier();
        }

        return $identifier;
    }

    /**
     * Get the query builder for transactions that a belong to a user.
     *
     * @param string $customer_id
     * @param string $transactionType
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getCustomerTransactionsQuery(string $customer_id, string $transactionType)
    {
        switch ($transactionType) {
            case TransactionOwnerType::LOAN:
                return $this->userRepository->findLoanTransactionsQuery($customer_id);
                break;

            case TransactionOwnerType::CONTRIBUTION_PLAN:
                return $this->userRepository->findContributionPlanTransactionsQuery($customer_id);
                break;

            case TransactionOwnerType::WALLET:
                return $this->userRepository->findWalletTransactionsQuery($customer_id);
                break;
        }
    }
}

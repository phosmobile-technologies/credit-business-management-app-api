<?php

namespace App\Services;


use App\Events\NewUserRegistered;
use App\Models\enums\TransactionOwnerType;
use App\Models\enums\RegistrationSource;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Repositories\Interfaces\UserProfileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
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

    private $defaultPassword;

    public function __construct(UserRepositoryInterface $userRepository, UserProfileRepositoryInterface $userProfileRepository, WalletRepositoryInterface $walletRepository)
    {
        $this->userRepository        = $userRepository;
        $this->userProfileRepository = $userProfileRepository;
        $this->walletRepository      = $walletRepository;
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

        $roles               = $attributes['roles'];
        $registration_source = $attributes['registration_source'];

        /** @var \Illuminate\Http\UploadedFile $file */
        $profilePicture = isset($attributes['profile_picture']) ? $attributes['profile_picture'] : null;

        if ($profilePicture) {
            $profilePicturePath = Storage::disk('local')->put('user-profile-pictures', $profilePicture);
        }

        $userData = $attributes->only([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'password',
        ])->toArray();

        // Generate a random password for the user registration via backend
        if ($attributes['registration_source'] === RegistrationSource::BACKEND) {
            $this->defaultPassword = Str::random(8);
            $userData['password']  = $this->defaultPassword;
        } else {
            // mailing event requires defaultPassword not to be null
            $this->defaultPassword = $attributes['password'];
        }

        // TODO: Figure why the 'directive' index is added by Lighthouse-php to the args it passes down
        $userProfileData                        = $attributes->except([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'roles',
            'directive',
            'password',
            'password_confirmation',
        ])->toArray();
        $userProfileData['customer_identifier'] = $this->generateCustomerIdentifier();

        if ($profilePicture) {
            $profilePicturePath = Storage::disk('public')->put('user-profile-pictures', $profilePicture);

            $baseUrl = config("app.url");
            $userProfileData['profile_picture_url'] = "{$baseUrl}/{$profilePicturePath}";

            unset($userProfileData['profile_picture']);
            Log::info(json_encode($userProfileData));
        }

        $user = $this->userRepository->createUser($userData);
        $this->userRepository->attachUserProfile($user, $userProfileData);
        $this->userRepository->attachUserRoles($user, $roles, $registration_source);
        $this->walletRepository->create([
            'user_id'        => $user->id,
            'wallet_balance' => 0
        ]);

        event(new NewUserRegistered($user, $this->defaultPassword, $registration_source));

        return [
            "user" => $user
        ];
    }

    /**
     * Register a new user.
     *
     * @param array $attributes
     * @return User
     */
    public function registerAdminUser(array $attributes): User
    {
        $attributes = collect($attributes);

        $roles = $attributes['roles'];

        $userData = $attributes->only([
            'first_name',
            'last_name',
            'email',
            'phone_number',
        ])->toArray();

        $this->defaultPassword = Str::random(8);
        $userData['password']  = $this->defaultPassword;


        // TODO: Figure why the 'directive' index is added by Lighthouse-php to the args it passes down to us
//        $userProfileData = $attributes->except([
//            'first_name',
//            'last_name',
//            'email',
//            'phone_number',
//            'roles',
//            'directive',
//            'password',
//            'password_confirmation',
//        ])->toArray();
//        $userProfileData['customer_identifier'] = $this->generateCustomerIdentifier();

        // @TODO: Stop using dummy user profile for admin users
        $userProfileData = factory(UserProfile::class)->make([
            'customer_identifier' => $this->generateCustomerIdentifier(),
            'company_id'          => $attributes['company_id'],
            'branch_id'           => $attributes['branch_id']
        ])->toArray();

        $user = $this->userRepository->createUser($userData);
        $this->userRepository->attachUserProfile($user, $userProfileData);
        $this->userRepository->attachUserRoles($user, $roles, RegistrationSource::BACKEND);

        event(new NewUserRegistered($user, $this->defaultPassword, RegistrationSource::BACKEND));

        return $user;
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

    /**
     * @param array $args
     * @return User|null
     */
    public function updateUserProfile(array $args)
    {
        $userProfile = $this->userProfileRepository->findByUserId($args['user_id']);
        $user        = $this->userRepository->find($args['user_id']);
        $args        = collect($args)->except('user_id', 'directive')->toArray();

        foreach ($args as $arg => $value) {
            if ($arg === 'phone_number') {
                $user->phone_number = $value;
            } else {
                $userProfile->{$arg} = $value;
            }
        }

        $user->save();
        $userProfile->save();

        return $user;
    }
}

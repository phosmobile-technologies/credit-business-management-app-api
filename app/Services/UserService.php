<?php

namespace App\Services;


use App\Events\NewUserRegistered;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param $attributes
     * @return array
     */
    public function registerUser(array $attributes): array
    {
        dd($attributes);
        $attributes = collect($attributes);

        $userData = $attributes->only([
            'first_name',
            'last_name',
            'email',
            'phone_number'
        ])->toArray();

        // Generate a random password for the user
        $defaultPassword  = Str::random(8);
        $userData['password'] = $defaultPassword;

        // TODO: Figure why the 'directive' index is added by Lighthouse-php to the args it passes down
        $userProfileData = $attributes->except([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'directive'
        ])->toArray();

        $user = $this->userRepository->createUser($userData);
        $this->userRepository->attachUserProfile($user, $userProfileData);

        event(new NewUserRegistered($user, $defaultPassword));

        return [
            "user" => $user
        ];
    }
}

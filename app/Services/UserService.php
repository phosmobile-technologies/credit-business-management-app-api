<?php

namespace App\Services;


use App\Events\NewUserRegistered;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\User;

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
        $attributes = collect($attributes);

        $userData = $attributes->only([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'password'
        ])->toArray();

        // TODO: Figure why the 'directive' index is added by Lighthouse-php to the args it passes down
        $userProfileData = $attributes->except([
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'password',
            'password_confirmation',
            'directive'
        ])->toArray();

        $user = $this->userRepository->createUser($userData);
        $this->userRepository->attachUserProfile($user, $userProfileData);

        event(new NewUserRegistered($user));

        return [
            "user" => $user
        ];
    }
}

<?php

namespace Tests\Unit\Services;

use App\Events\NewUserRegistered;
use App\Models\UserProfile;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Event;

class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = Mockery::mock(UserRepository::class);

        $this->app->instance(UserRepository::class, $this->userRepository);

        $this->userService = $this->app->make(UserService::class);
    }

    public function testItCanRegisterUser()
    {
        Event::fake();


        $user = factory(User::class)->make();
        $userData = collect($user)->except('email_verified_at')->toArray();
        $userProfileData = factory(UserProfile::class)->make()->toArray();
        $registrationData = array_merge($userData, $userProfileData, [
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->userRepository->shouldReceive('createUser')
            ->andReturn($user);

        $this->userRepository->shouldReceive('attachUserProfile')
            ->withArgs([$user, $userProfileData])
            ->andReturn($user);

        $response = $this->userService->registerUser($registrationData);

        $this->assertEquals($userData['first_name'], $response['user']['first_name']);
        $this->assertEquals($userData['last_name'], $response['user']['last_name']);
        $this->assertEquals($userData['email'], $response['user']['email']);

        Event::assertDispatched(NewUserRegistered::class);
    }
}

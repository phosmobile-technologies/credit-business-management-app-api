<?php

namespace Tests\Unit\Services;

use App\Services\AfricasTalkingService;
use Mockery;
use Tests\TestCase;

class AfricasTalkingServiceTest extends TestCase
{
    /**
     * @var AfricasTalkingService
     */
    private $africasTalkingService;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->africasTalkingService = $this->app->make(AfricasTalkingService::class);
    }

    /**
     * @test
     */
    public function testItSendsSms() {
        $to = "+23408089026730";
        $message = "Hello there Abraham";

        $response = $this->africasTalkingService->sendSms($to, $message);
        $this->assertEquals('success', $response['status']);
    }
}

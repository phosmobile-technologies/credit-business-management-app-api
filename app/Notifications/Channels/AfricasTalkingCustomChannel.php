<?php

namespace App\Notifications\Channels;


use App\Services\AfricasTalkingService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AfricasTalkingCustomChannel
{
    /**
     * @var AfricasTalkingService
     */
    private $africasTalkingService;

    /**
     * AfricasTalkingCustomChannel constructor.
     *
     * @param AfricasTalkingService $africasTalkingService
     */
    public function __construct(AfricasTalkingService $africasTalkingService)
    {
        $this->africasTalkingService = $africasTalkingService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toAfricasTalkingCustom($notifiable);

        $phoneNumber = "+234{$notifiable->phone_number}";
        $response =  $this->africasTalkingService->sendSms($phoneNumber, $message);

        return $response;
    }
}

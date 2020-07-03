<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;

/**
 * Class AfricasTalkingService
 *
 * This service is for the Africa's Talking communication package. It is used here to send SMS notifications to customers
 *
 * @url https://africastalking.com/
 * @package App\Services
 */
class AfricasTalkingService
{
    /**
     * @var
     */
    private $africasTalkingApi;

    private $senderId;

    /**
     * AfricasTalkingService constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $username = env('AFRICAS_TALKING_USERNAME');
        $apiKey = env('AFRICAS_TALKING_API_KEY');
        $this->senderId = env('AFRICAS_TALKING_SENDER_ID');

        if (!isset($username) || !isset($apiKey) || !isset($this->senderId)) {
            throw new \Exception("Please set environment variables for Africa's talking Username, API_KEY and SenderId");
        }

        $this->africasTalkingApi = new AfricasTalking($username, $apiKey);
    }

    /**
     * Send an sms using the Africa's talking SDK
     *
     * @param string $to The phone number of the user.
     * @param string $message The message to be sent to the user.
     * @return array
     */
    public function sendSms(string $to, string $message)
    {
        $smsService = $this->africasTalkingApi->sms();

        return $smsService->send([
            'to' => $to,
            'message' => $message,
            'from' => $this->senderId
        ]);
    }
}

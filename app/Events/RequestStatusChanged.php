<?php

namespace App\Events;

use App\Models\CustomerWithdrawalRequest;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CustomerWithdrawalRequest
     */
    public $CustomerWithdrawalRequest;

    /**
     * @var string
     */
    public $oldRequestStatus;

    public $causer;

    /**
     * @var null|string
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param CustomerWithdrawalRequest $customerwithdrawalrequest
     * @param string $oldRequestStatus
     * @param $causer
     * @param null|string $message
     */
    public function __construct(CustomerWithdrawalRequest $customerwithdrawalrequest, string $oldRequestStatus, $causer, ?string $message)
    {
        $this->CustomerWithdrawalRequest = $customerwithdrawalrequest;
        $this->oldRequestStatus = $oldRequestStatus;
        $this->causer = $causer;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

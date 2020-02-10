<?php

namespace App\Events;

use App\Models\Loan;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Loan
     */
    public $loan;

    /**
     * @var string
     */
    public $oldApplicationStatus;

    public $causer;

    /**
     * @var null|string
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @param Loan $loan
     * @param string $oldApplicationStatus
     * @param $causer
     * @param null|string $message
     */
    public function __construct(Loan $loan, string $oldApplicationStatus, $causer, ?string $message)
    {
        //
        $this->loan = $loan;
        $this->oldApplicationStatus = $oldApplicationStatus;
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

<?php

namespace App\Events;

use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionProcessedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @var ProcessedTransaction
     */
    public $processedTransaction;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param Transaction $transaction
     * @param ProcessedTransaction $processedTransaction
     */
    public function __construct(User $user, Transaction $transaction, ProcessedTransaction $processedTransaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->processedTransaction = $processedTransaction;
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

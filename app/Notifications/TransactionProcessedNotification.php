<?php

namespace App\Notifications;

use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\Notifications\Channels\AfricasTalkingCustomChannel;
use App\Notifications\Messages\AfricasTalkingCustomChannelMessage;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @var User
     */
    public $user;

    /**
     * @var ProcessedTransaction
     */
    public $processedTransaction;

    /**
     * Create a new notification instance.
     *
     * @param User $user
     * @param Transaction $transaction
     * @param ProcessedTransaction $processedTransaction
     */
    public function __construct(User $user, Transaction $transaction, ProcessedTransaction $processedTransaction)
    {
        $this->transaction = $transaction;
        $this->user = $user;
        $this->processedTransaction = $processedTransaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [AfricasTalkingCustomChannel::class, 'database'];
    }

    /**
     * Get the message representation of the SMS
     *
     * @return mixed
     */
    public function toAfricasTalkingCustom()
    {
        return (new AfricasTalkingCustomChannelMessage())
            ->message("Test message from springverse");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            '$transaction' => $this->transaction,
            'processedTransaction' => $this->processedTransaction,
        ];
    }

}

<?php

namespace App\Notifications;

use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\Notifications\Channels\AfricasTalkingCustomChannel;
use App\Notifications\Messages\AfricasTalkingCustomChannelMessage;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Jusibe\JusibeChannel;
use NotificationChannels\Jusibe\JusibeMessage;

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
     * @param User                 $user
     * @param Transaction          $transaction
     * @param ProcessedTransaction $processedTransaction
     */
    public function __construct(User $user, Transaction $transaction, ProcessedTransaction $processedTransaction)
    {
        $this->transaction          = $transaction;
        $this->user                 = $user;
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
        // return [JusibeChannel::class, 'database'];
        return [AfricasTalkingCustomChannel::class, 'mail', 'database'];
    }

    /**
     * Generate the SMS message to be sent out
     *
     * @return string
     */
    public function generateNotificationMessage()
    {
        $formattedTransactionType = strtolower($this->transaction->transaction_type);
        str_replace($formattedTransactionType, '_', ' ');
        $formattedTransactionDate = Carbon::parse($this->transaction->created_at)->format('Y-M-d');

        $message = "Dear {$this->user->first_name} {$this->user->last_name}, the {$formattedTransactionType} request you made on {$formattedTransactionDate} for the sum of ₦{$this->transaction->transaction_amount} has been {$this->processedTransaction->processing_type}D";

        // Removed the message for now because it makes the sms too lengthy.
        // if (isset($this->processedTransaction->message) && $this->processedTransaction->message !== '') {
        // $message .= " because {$this->processedTransaction->message}";
        // }

        return $message;
    }


    /**
     * Get the message representation of the SMS for Africa's talking.
     *
     * @return mixed
     */
    public function toAfricasTalkingCustom()
    {
        $message = $this->generateNotificationMessage();

        return (new AfricasTalkingCustomChannelMessage())
            ->message($message);
    }

    /**
     * Get the message representation of the SMS for Jusibe
     *
     * @return mixed
     */
    public function toJusibe()
    {
        $message = $this->generateNotificationMessage();

        return (new JusibeMessage())
            ->content($message);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'notificationMessage'  => $this->generateNotificationMessage(),
            'transaction'          => $this->transaction,
            'processedTransaction' => $this->processedTransaction,
        ];
    }

    /**
     * Convert the notification to a message to be sent via email
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)->view(
            'email.transaction.processed', [
            'user'                 => $this->user,
            'transaction'          => $this->transaction,
            'processedTransaction' => $this->processedTransaction,
        ]);

    }

}

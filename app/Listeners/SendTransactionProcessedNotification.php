<?php

namespace App\Listeners;

use App\Events\TransactionProcessedEvent;
use App\Models\enums\TransactionType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionProcessedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TransactionProcessedEvent $event
     * @return void
     */
    public function handle(TransactionProcessedEvent $event)
    {
        $user = $event->user;
        $transaction = $event->transaction;
        $processedTransaction = $event->processedTransaction;

        if($transaction->transaction_type === TransactionType::LOAN_REPAYMENT) {
            $user->notify(new \App\Notifications\TransactionProcessedNotification($user, $transaction, $processedTransaction));
        }

    }

    public function failed()
    {

    }
}

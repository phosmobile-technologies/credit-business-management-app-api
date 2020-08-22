<?php

namespace App\Listeners;

use App\Events\TransactionProcessedEvent;
use App\Models\enums\TransactionType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTransactionProcessedNotification
{

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

        /**
         * @TODO to be updated later
         */
        if($transaction->transaction_type === TransactionType::LOAN_REPAYMENT) {
            $user->notify(new \App\Notifications\TransactionProcessedNotification($user, $transaction, $processedTransaction));
        } else {
            $user->notify(new \App\Notifications\TransactionProcessedNotification($user, $transaction, $processedTransaction));
        }

    }

    public function failed(TransactionProcessedEvent $event, $exception)
    {
        Log::info("The SendTransactionProcessedNotification Listener Failed");
        Log::info($exception->getMessage());
    }
}

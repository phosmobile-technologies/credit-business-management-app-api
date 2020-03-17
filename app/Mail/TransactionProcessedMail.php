<?php

namespace App\Mail;

use App\Models\ProcessedTransaction;
use App\Models\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionProcessedMail extends Mailable
{
    use Queueable, SerializesModels;
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
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.transaction.processed');
    }
}

<?php

namespace App\Services;


use App\Events\NewLoanCreated;
use App\Models\Loan;
use App\Repositories\Interfaces\LoanRepositoryInterface;

class LoanService
{
    /**
     * @var LoanRepositoryInterface
     */
    private $loanRepository;

    /**
     * LoanService constructor.
     * @param LoanRepositoryInterface $loanRepository
     */
    public function __construct(LoanRepositoryInterface $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    /**
     * Create a new loan.
     *
     * @param array $loanData
     * @return Loan
     */
    public function create(array $loanData): Loan
    {
        // We need to generate a unique (app specified) identifier for each loan
        $loanData['loan_identifier'] = $this->generateLoanIdentifier();

        $loan = $this->loanRepository->create($loanData);

        event(new NewLoanCreated($loan));

        return $loan;
    }

    /**
     * Generate a random loan identifier for a loan.
     *
     * @return int
     */
    public function generateLoanIdentifier(): int
    {
        $identifier = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the loan_identifier exists already
        if ($this->loanRepository->loanIdentifierExists($identifier)) {
            return self::generateLoanIdentifier();
        }

        return $identifier;
    }
}

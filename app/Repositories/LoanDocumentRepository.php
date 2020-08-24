<?php

namespace App\Repositories;


use App\Models\LoanDocument;
use App\Repositories\Interfaces\LoanDocumentRepositoryInterface;

class LoanDocumentRepository implements LoanDocumentRepositoryInterface
{

    /**
     * Create a loan document entry in the database.
     *
     * @param array $loanDocumentData
     * @return LoanDocument
     */
    public function create(array $loanDocumentData): LoanDocument
    {
        return LoanDocument::create($loanDocumentData);
    }
}

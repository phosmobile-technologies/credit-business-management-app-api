<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 24/08/2020
 * Time: 22:07
 */

namespace App\Repositories\Interfaces;


use App\Models\LoanDocument;

interface LoanDocumentRepositoryInterface
{
    /**
     * Create a loan document entry in the database.
     *
     * @param array $loanDocumentData
     * @return LoanDocument
     */
    public function create(array $loanDocumentData): LoanDocument;
}

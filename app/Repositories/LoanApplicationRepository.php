<?php
/**
 * Created by PhpStorm.
 * User: abraham
 * Date: 25/12/2019
 * Time: 4:32 PM
 */

namespace App\Repositories;


use App\Models\LoanApplication;
use App\Repositories\Interfaces\LoanApplicationRepositoryInterface;

class LoanApplicationRepository implements LoanApplicationRepositoryInterface
{

    /**
     * Create a LoanApplication entry in the database.
     *
     * @param array $loanApplicationData
     * @return LoanApplication
     */
    public function create(array $loanApplicationData): LoanApplication
    {
        $loanApplication = LoanApplication::create($loanApplicationData);

        return $loanApplication;
    }
}

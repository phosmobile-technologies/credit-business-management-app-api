<?php

namespace App\Repositories\Interfaces;


use App\Models\CustomerWithdrawalRequest;

/**
 * Interface CustomerWithdrawalRequestRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface CustomerWithdrawalRequestRepositoryInterface
{
    /**
     * Create a CustomerWithdrawalRequest in the database.
     *
     * @param array $CustomerWithdrawalRequestData
     * @return CustomerWithdrawalRequest
     */
    public function create(array $CustomerWithdrawalRequestData): CustomerWithdrawalRequest;

    /**
     * Update a CustomerWithdrawalRequest in the database.
     *
     * @param string $id
     * @param array $CustomerWithdrawalRequestData
     * @return CustomerWithdrawalRequest
     */
    public function update(string $id ,array $CustomerWithdrawalRequestData): CustomerWithdrawalRequest;

    /**
     * Find a CustomerWithdrawalRequest by id.
     *
     * @param string $user_id
     * @return CustomerWithdrawalRequest|null
     */
    public function find(string $user_id): ?CustomerWithdrawalRequest;
}
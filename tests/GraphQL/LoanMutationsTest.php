<?php

namespace Tests\Feature;

use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Mutations\LoanMutations;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class LoanMutationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithTestUsers;

    public function testItSuccessfullyCreatesANewLoan()
    {
        $this->seed('DatabaseSeeder');

        $user = $this->createUser();

        $loanData = collect(factory(Loan::class)->state('not_disbursed_loan')->make())
            ->except(['loan_identifier'])
            ->toArray();
        $loanData['user_id'] = $user->id;

        $response = $this->postGraphQL([
            'query' => LoanMutations::CreateLoanMutation(),
            'variables' => [
                'input' => $loanData
            ],
        ]);

        $response->assertJson([
            'data' => [
                'CreateLoan' => [
                    'loan_amount' => $loanData['loan_amount']
                ]
            ]
        ]);
    }

}

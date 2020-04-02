<?php

namespace Tests\Unit\Models;

use App\Models\ContributionPlan;
use Tests\TestCase;
use Carbon\Carbon;

class ContributionPlanModelTest extends TestCase
{
    /**
     * @test
     */
    public function testItReturnsTheCorrectContributionPlanStatus()
    {
        $contributionPlanOne = factory(ContributionPlan::class)->make([
            'balance' => 0,
            'payback_date' => Carbon::tomorrow()
        ]);
        $contributionPlanTwo = factory(ContributionPlan::class)->make([
            'balance' => 1000,
            'payback_date' => Carbon::tomorrow()
        ]);
        $contributionPlanThree = factory(ContributionPlan::class)->make([
            'balance' => 1000,
            'payback_date' => Carbon::yesterday()
        ]);

        $this->assertEquals(ContributionPlan::STATUS_INACTIVE, $contributionPlanOne->contributionStatus);
        $this->assertEquals(ContributionPlan::STATUS_ACTIVE, $contributionPlanTwo->contributionStatus);
        $this->assertEquals(ContributionPlan::STATUS_COMPLETED, $contributionPlanThree->contributionStatus);
    }

    /**
     * @test
     */
    public function testItCorrectlyDeterminesIfStartDateIsReached()
    {
        $contributionPlanOne = factory(ContributionPlan::class)->make([
            'start_date' => Carbon::yesterday()
        ]);
        $contributionPlanTwo = factory(ContributionPlan::class)->make([
            'start_date' => Carbon::today()
        ]);
        $contributionPlanThree = factory(ContributionPlan::class)->make([
            'start_date' => Carbon::tomorrow()
        ]);

        $this->assertTrue($contributionPlanOne->startDateReached());
        $this->assertTrue($contributionPlanTwo->startDateReached());
        $this->assertFalse($contributionPlanThree->startDateReached());
    }

    /**
     * @test
     */
    public function testItCorrectlyDeterminesIfPaymentDateIsReached()
    {
        $contributionPlanOne = factory(ContributionPlan::class)->make([
            'payback_date' => Carbon::yesterday()
        ]);
        $contributionPlanTwo = factory(ContributionPlan::class)->make([
            'payback_date' => Carbon::today()
        ]);
        $contributionPlanThree = factory(ContributionPlan::class)->make([
            'payback_date' => Carbon::tomorrow()
        ]);

        $this->assertTrue($contributionPlanOne->paymentDateReached());
        $this->assertTrue($contributionPlanTwo->paymentDateReached());
        $this->assertFalse($contributionPlanThree->paymentDateReached());
    }

    /**
     * @test
     */
    public function testItCorrectlyGetsInterest() {
        $contributionPlanOne = factory(ContributionPlan::class)->make([
            'status' => ContributionPlan::STATUS_ACTIVE,
            'activation_date' => Carbon::today()->subDays(10),
            'payback_date' => Carbon::today(),
            'interest_rate' => 10,
            'balance' => 1000,
        ]);
        $contributionPlanTwo = factory(ContributionPlan::class)->make([
            'status' => ContributionPlan::STATUS_INACTIVE,
            'activation_date' => Carbon::today()->subDays(10),
            'payback_date' => Carbon::today(),
            'interest_rate' => 10,
            'balance' => 1000,
        ]);
        $contributionPlanThree = factory(ContributionPlan::class)->make([
            'status' => ContributionPlan::STATUS_ACTIVE,
            'activation_date' => Carbon::today()->subDays(20),
            'payback_date' => Carbon::today()->addDays(5),
            'interest_rate' => 10,
            'balance' => 1000,
        ]);

        $this->assertEquals(2.74, number_format($contributionPlanOne->interest, 2));
        $this->assertEquals(0, $contributionPlanTwo->interest);
        $this->assertEquals(5.48, number_format($contributionPlanThree->interest, 2));
    }
}

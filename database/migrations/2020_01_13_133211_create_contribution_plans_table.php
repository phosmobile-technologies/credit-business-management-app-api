<?php

use App\Models\ContributionPlan;
use App\Models\enums\ContributionFrequency;
use App\Models\enums\ContributionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_plans', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->bigIncrements('contribution_id');
            $table->enum('type', [
                ContributionType::FIXED,
                ContributionType::GOAL,
                ContributionType::LOCKED
            ]);
            $table->enum('frequency', [
                ContributionFrequency::DAILY,
                ContributionFrequency::WEEKLY,
                ContributionFrequency::MONTHLY,
                ContributionFrequency::QUARTERLY
            ])->nullable();
            $table->enum('status', [
                ContributionPlan::STATUS_INACTIVE,
                ContributionPlan::STATUS_ACTIVE,
                ContributionPlan::STATUS_COMPLETED
            ])->default(ContributionPlan::STATUS_INACTIVE);
            $table->float('goal');
            $table->float('balance')->nullable();
            $table->string('name');
            $table->integer('duration'); # In months
            $table->float('interest_rate'); # In percentage
            $table->date('start_date')->nullable();
            $table->date('payback_date')->nullable();
            $table->date('activation_date')->nullable();
            $table->float('fixed_amount')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution_plans');
    }
}

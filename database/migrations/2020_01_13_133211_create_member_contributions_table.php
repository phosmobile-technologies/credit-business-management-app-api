<?php

use App\Models\enums\ContributionFrequency;
use App\Models\enums\ContributionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_contributions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->bigIncrements('contribution_id');
            $table->enum('contribution_type', [
                ContributionType::FIXED,
                ContributionType::GOAL,
                ContributionType::LOCKED,
                ContributionType::WALLET
            ]);
            $table->enum('contribution_frequency', [
                ContributionFrequency::DAILY,
                ContributionFrequency::WEEKLY,
                ContributionFrequency::MONTHLY,
                ContributionFrequency::QUARTERLY
            ]);
            $table->float('contribution_amount');
            $table->float('contribution_balance');
            $table->string('contribution_name');
            $table->integer('contribution_duration'); # In months
            $table->float('contribution_interest_rate'); # In percentage
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
        Schema::dropIfExists('member_contributions');
    }
}

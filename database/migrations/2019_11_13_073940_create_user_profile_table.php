<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->uuid('user_id');
            $table->string('customer_identifier')->unique();
            $table->enum('gender', ['MALE', 'FEMALE'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'DIVORCED', 'SEPERATED', 'WIDOWED'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('address');
            $table->string('state_of_origin')->nullable();
            $table->float('saving_amount')->nullable();
            $table->enum('frequency_of_saving', ['WEEKLY', 'MONTHLY', 'QUARTERLY'])->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('relationship_with_next_of_kin')->nullable();
            $table->string('account_administrator')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
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
        Schema::dropIfExists('user_profile');
    }
}

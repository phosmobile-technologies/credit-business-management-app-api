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
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->date('date_of_birth');
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'DIVORCED']);
            $table->string('occupation');
            $table->string('address');
            $table->string('state_of_origin');
            $table->float('saving_amount');
            $table->enum('frequency_of_saving', ['WEEKLY', 'MONTHLY', 'QUARTERLY']);
            $table->string('next_of_kin');
            $table->string('relationship_with_next_of_kin');
            $table->string('account_administrator');
            $table->string('account_name');
            $table->string('account_number');
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

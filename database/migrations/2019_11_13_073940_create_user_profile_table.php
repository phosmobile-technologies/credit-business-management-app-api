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
            $table->enum('registration_source', ['ONLINE', 'BACKEND']);
            $table->enum('gender', ['MALE', 'FEMALE'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('marital_status', ['SINGLE', 'MARRIED', 'DIVORCED', 'SEPERATED', 'WIDOWED'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('address');
            $table->string('state_of_origin')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('relationship_with_next_of_kin')->nullable();
            $table->string('account_administrator')->nullable(); // Possibly not needed
            $table->uuid('account_administrator_id')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bvn')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE']);
            $table->uuid('company_id');
            $table->uuid('branch_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('account_administrator_id')
                ->references('id')
                ->on('users');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');

            $table->foreign('branch_id')
                ->references('id')
                ->on('company_branches');
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_customer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)
                ->comment('Fill with name of customer');
            $table->string('email', 50)
                ->default(null)
                ->comment('Fill with user email for customer');
            $table->string('phone_number', 25)
                ->default(null)
                ->comment('Fill with phone number of customer')
                ->nullable();
            $table->date('date_of_birth')
                ->default(null)
                ->comment('Fill with date if birdh of customer');
            $table->string('photo', 100)
                ->default(null)
                ->comment('Fill with user profile picture')
                ->nullable();
            $table->tinyInteger('is_verified')
                ->default(null)
                ->comment('Fill with "1" if customer verified, Fill "0" if customer not verified');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_customer');
    }
}

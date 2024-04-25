<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_discount', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('m_customer_id')
                ->comment('Fill with id of m_customer');
            $table->string('m_promo_id')
                ->comment('Fill with id of m_promo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_discount');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMPromo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_promo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150)->comment('Fill with name of promo');
            $table->enum('status',['Voucher','Diskon'])->comment('Fill with type of promo');
            $table->integer('expired_in_day')->comment('Fill with total of activr day, fill with 1 for 1 day, 7 for 1 week, 30 for 1 month');
            $table->double('nominal_percentage')
                ->nullable()
                ->comment('Fill when status = diskon');
            $table->double('nominal_rupiah')
                ->nullable()
                ->comment('fill when status = voucher');
            $table->text('term_conditions', 255)
                ->nullable()
                ->comment('Fill with term and conditions= to get this promo');
            $table->string('photo', 100)
                ->nullable();
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
        Schema::dropIfExists('m_promo');
    }
}

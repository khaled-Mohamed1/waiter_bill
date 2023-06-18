<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('company_codes')->cascadeOnDelete();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->date('shift_date')->nullable();
            $table->time('shift_time_start')->nullable();
            $table->time('shift_time_end')->nullable();
            $table->float('beginning_cash')->default(0);
            $table->float('payments_cash')->default(0);
            $table->float('refunds_cash')->default(0);
            $table->float('payments')->default(0);
            $table->float('withdrawal_amounts')->default(0);
            $table->float('total_sales')->default(0);
            $table->float('discounts')->default(0);
            $table->float('cash_money')->default(0);
            $table->float('card')->default(0);
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

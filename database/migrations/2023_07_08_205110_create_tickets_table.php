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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('company_codes')->cascadeOnDelete();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->bigInteger('table_id')->unsigned()->nullable();
            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
            $table->string('ticket_name')->nullable();
            $table->float('ticket_total')->default(0);
            $table->string('ticket_type')->nullable();
            $table->float('ticket_total_discount')->default(0);
            $table->float('ticket_total_summation')->default(0);
            $table->float('ticket_paid')->default(0);
            $table->float('ticket_rest')->default(0);
            $table->string('ticket_payment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

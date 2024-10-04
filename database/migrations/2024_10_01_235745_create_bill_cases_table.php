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
        Schema::create('bill_cases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');

            $table->unsignedBigInteger('case_id')->nullable();
            $table->foreign('case_id')->references('id')->on('states')->onDelete('cascade');

            $table->unsignedBigInteger('case_cost')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_cases');
    }
};

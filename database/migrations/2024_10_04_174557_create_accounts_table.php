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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');

            $table->text('note')->nullable();
            $table->string('type');

            $table->integer('signed_value');
            $table->bigInteger('current_account');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

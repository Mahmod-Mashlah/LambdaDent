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
        Schema::create('states', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('patient_name');
            $table->string('age');
            $table->string('gender');
            $table->boolean('need_trial');
            $table->boolean('repeat');
            $table->string('shade');
            $table->date('expected_delivery_date');
            $table->text('notes');
            $table->string('status');
            $table->boolean('confirm_delivery');

            $table->string('teeth_crown')->nullable();
            $table->string('teeth_pontic')->nullable();
            $table->string('teeth_implant')->nullable();
            $table->string('teeth_veneer')->nullable();
            $table->string('teeth_inlay')->nullable();
            $table->string('teeth_denture')->nullable();

            $table->string('bridges_crown')->nullable();
            $table->string('bridges_pontic')->nullable();
            $table->string('bridges_implant')->nullable();
            $table->string('bridges_veneer')->nullable();
            $table->string('bridges_inlay')->nullable();
            $table->string('bridges_denture')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};

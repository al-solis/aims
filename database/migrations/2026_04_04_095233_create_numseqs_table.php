<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('numseqs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('prefix')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('current_number')->default(0);
            $table->integer('number_length')->default(5);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numseqs');
    }
};

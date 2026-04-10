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
        Schema::create('clearance_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clearance_id');
            $table->foreign('clearance_id')->references('id')->on('clearance_headers');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('approver_id');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->integer('approved')->default(0); // 0 = pending, 1 = approved, 2 = rejected
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearance_approvals');
    }
};

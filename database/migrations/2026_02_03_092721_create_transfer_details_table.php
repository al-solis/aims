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
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->foreign('transfer_id')->references('id')->on('transfers');
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->unsignedBigInteger('from_employee_id')->nullable();
            $table->foreign('from_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('from_location_id')->nullable();
            $table->foreign('from_location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('from_subloc_id')->nullable();
            $table->foreign('from_subloc_id')->references('id')->on('sublocations');
            $table->unsignedBigInteger('to_employee_id');
            $table->foreign('to_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('to_location_id')->nullable();
            $table->foreign('to_location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('to_subloc_id')->nullable();
            $table->foreign('to_subloc_id')->references('id')->on('sublocations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_details');
    }
};

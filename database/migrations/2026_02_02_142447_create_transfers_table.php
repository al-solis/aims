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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique('code');
            $table->unsignedBigInteger('asset_id');
            $table->date('date')->nullable(false);
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->string('description', 255)->nullable();
            $table->unsignedBigInteger('from_employee_id')->nullable();
            $table->foreign('from_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('to_employee_id');
            $table->foreign('to_employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('subloc_id')->nullable();
            $table->foreign('subloc_id')->references('id')->on('sublocations');
            $table->unsignedBigInteger('created_by');
            $table->integer('cancelled')->default(0);
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

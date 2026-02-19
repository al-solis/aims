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
        Schema::create('issuance_headers', function (Blueprint $table) {
            $table->id();
            $table->string('issuance_number')->unique();
            $table->text('purpose')->nullable();
            $table->unsignedBigInteger('issued_to');
            $table->foreign('issued_to')->references('id')->on('employees');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->date('issuance_date');
            $table->text('remarks')->nullable();
            $table->integer('status')->default(1); // 1 = active, 0 = inactive
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('issuance_headers');
    }
};

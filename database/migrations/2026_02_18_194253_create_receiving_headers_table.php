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
        Schema::create('receiving_headers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->text('description')->nullable();
            $table->date('received_date');
            $table->string('reference')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('received_by')->nullable();
            $table->foreign('received_by')->references('id')->on('employees');
            $table->string('remarks')->nullable();
            $table->integer('status')->default(0); // 0 = open, 1 = posted, 2 = cancelled
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users');
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
        Schema::dropIfExists('receiving_headers');
    }
};

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
        Schema::create('budget_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_header_id');
            $table->foreign('budget_header_id')->references('id')->on('budget_headers');
            $table->integer('quantity');
            $table->string('item_code', 50)->nullable();
            $table->string('item_description');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('uoms');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_details');
    }
};

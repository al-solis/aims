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
        Schema::create('receiving_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiving_header_id');
            $table->foreign('receiving_header_id')->references('id')->on('receiving_headers')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('supplies');
            $table->decimal('quantity', 15, 2);
            $table->unsignedBigInteger('uom_id');
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receiving_details');
    }
};

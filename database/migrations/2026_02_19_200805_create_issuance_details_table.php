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
        Schema::create('issuance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('issuance_header_id');
            $table->foreign('issuance_header_id')->references('id')->on('issuance_headers');
            $table->unsignedBigInteger('supply_id');
            $table->foreign('supply_id')->references('id')->on('supplies');
            $table->decimal('quantity', 10, 2);
            $table->unsignedBigInteger('uom_id');
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuance_details');
    }
};

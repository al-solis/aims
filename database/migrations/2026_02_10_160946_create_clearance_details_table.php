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
        Schema::create('clearance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clearance_header_id');
            $table->foreign('clearance_header_id')->references('id')->on('clearance_headers');
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('return_quantity', 10, 2)->default(0);
            $table->decimal('purchase_cost', 10, 2)->default(0);
            $table->decimal('actual_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->integer('status')->default(0); //0=Pending, 1=Returned, 2=Damaged, 3=Lost
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('clearance_details');
    }
};

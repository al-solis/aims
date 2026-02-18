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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('supplies_categories');
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->decimal('reorder_level', 10, 2)->default(0);
            $table->decimal('reorder_quantity', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('allocated_stock', 10, 2)->default(0);
            $table->decimal('available_stock', 10, 2)->default(0);
            $table->decimal('total_stock', 10, 2)->default(0);
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('supplies');
    }
};

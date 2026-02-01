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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 15);
            $table->string('name', 50);
            $table->string('description', 150)->nullable();

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');

            $table->string('subcategory', 50)->nullable();

            $table->decimal('cost', 10, 2)->nullable();
            $table->date('purchase_date');
            $table->integer('status')->default('1'); //1: Available, 2: Active, 3: Assigned, 4: Maintenance, 5: Retired

            $table->string('manufacturer', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('serial')->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable(true);
            $table->foreign('assigned_to')->references('id')->on('employees');
            $table->unsignedBigInteger('location_id')->nullable(true);
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedBigInteger('subloc_id')->nullable(true);
            $table->foreign('subloc_id')->references('id')->on('sublocations');

            $table->integer('condition')->default('1'); //1: Excellent, 2: Good, 3: Fair, 4: Poor
            $table->string('warranty', 255)->nullable(true);

            $table->unsignedBigInteger('created_by')->nullable(true);
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('updated_by')->nullable(true);
            $table->foreign('updated_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

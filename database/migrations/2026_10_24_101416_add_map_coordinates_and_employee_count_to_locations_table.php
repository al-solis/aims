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
        Schema::table('locations', function (Blueprint $table) {
            // Google Maps coordinates
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');

            // Optional: Google Places ID, useful for reliable lookups / geocoding
            $table->string('place_id', 255)->nullable()->after('longitude');

            // Number of employees assigned to this location
            $table->unsignedInteger('employee_count')->default(0)->after('contact_number');

            // Speeds up map queries (e.g. bounding-box filtering)
            $table->index(['latitude', 'longitude'], 'locations_lat_lng_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex('locations_lat_lng_index');
            $table->dropColumn([
                'latitude',
                'longitude',
                'place_id',
                'employee_count',
            ]);
        });
    }
};
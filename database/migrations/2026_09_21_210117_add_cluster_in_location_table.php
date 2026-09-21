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
            if (!Schema::hasColumn('locations', 'cluster_id')) {
                $table->unsignedBigInteger('cluster_id')->nullable()->after('id');
                $table->foreign('cluster_id')->references('id')->on('clusters');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'cluster_id')) {
                $table->dropForeign(['cluster_id']);
                $table->dropColumn('cluster_id');
            }
        });
    }
};

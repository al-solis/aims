<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('img')->nullable();
            $table->string('src')->nullable();
            $table->integer('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('modules')->insert([
            'code' => 'AM',
            'name' => 'Asset Management',
            'description' => 'Asset Management',
            'icon' => null,
            'img' => 'assets.png',
            'src' => 'https://img.icons8.com/color/48/box.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'SUP',
            'name' => 'Supplies Management',
            'description' => 'Supplies Management',
            'icon' => null,
            'img' => 'supplies.png',
            'src' => 'https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-office-supplies-office-and-office-supplies-flaticons-lineal-color-flat-icons-11.png',
            'sequence' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'BM',
            'name' => 'Budget Management',
            'description' => 'Budget Management',
            'icon' => null,
            'img' => 'budget.png',
            'src' => 'https://img.icons8.com/?size=100&id=68348&format=png&color=000000',
            'sequence' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'LIC',
            'name' => 'Licenses and Permit Management',
            'description' => 'Licenses and Permit Management',
            'icon' => null,
            'img' => 'licenses.png',
            'src' => 'https://img.icons8.com/external-good-lines-kalash/32/external-card-banking-and-money-good-lines-kalash.png',
            'sequence' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'CM',
            'name' => 'Clearance Management',
            'description' => 'Clearance Management',
            'icon' => null,
            'img' => 'clearance.png',
            'src' => 'https://img.icons8.com/3d-sugary/100/document-14.png',
            'sequence' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'MM',
            'name' => 'Maintenance Management',
            'description' => 'Maintenance Management',
            'icon' => null,
            'img' => 'maintenance.png',
            'src' => 'https://img.icons8.com/external-wanicon-lineal-color-wanicon/64/external-wrench-construction-wanicon-lineal-color-wanicon.png',
            'sequence' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'EM',
            'name' => 'Employee Management',
            'description' => 'Employee Management',
            'icon' => null,
            'img' => 'employees.png',
            'src' => 'https://img.icons8.com/external-filled-outline-wichaiwi/64/external-Employee-business-filled-outline-wichaiwi.png',
            'sequence' => 7,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'REP',
            'name' => 'Reports',
            'description' => 'Reports',
            'icon' => null,
            'img' => 'reports.png',
            'src' => 'https://img.icons8.com/fluency/48/pie-chart-report-script.png',
            'sequence' => 8,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('modules')->insert([
            'code' => 'SET',
            'name' => 'System Settings and Setup',
            'description' => 'System Settings and Setup',
            'icon' => null,
            'img' => 'settings.png',
            'src' => 'https://img.icons8.com/bubbles/100/settings.png',
            'sequence' => 9,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};

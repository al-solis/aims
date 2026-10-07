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
        Schema::create('sub_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules');
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('group', 50)->nullable();
            $table->string('icon')->nullable();
            $table->string('img')->nullable();
            $table->string('src')->nullable();
            $table->integer('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('sub_modules')->insert([
            'module_id' => 1,
            'code' => 'AM-01',
            'name' => 'Asset',
            'description' => 'Asset Management',
            'group' => null,
            'icon' => 'bi bi-boxes',
            'img' => 'assets.png',
            'src' => 'https://img.icons8.com/color/48/box.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 2,
            'code' => 'SUP-01',
            'name' => 'Supplies',
            'description' => 'Supplies Management',
            'group' => null,
            'icon' => 'bi bi-box',
            'img' => 'supplies.png',
            'src' => 'https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-office-supplies-office-and-office-supplies-flaticons-lineal-color-flat-icons-11.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 3,
            'code' => 'BM-01',
            'name' => 'Budget',
            'description' => 'Budget Management',
            'group' => null,
            'icon' => 'bi bi-wallet',
            'img' => 'budget.png',
            'src' => 'https://img.icons8.com/?size=100&id=68348&format=png&color=000000',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 4,
            'code' => 'LIC-01',
            'name' => 'Licenses',
            'description' => 'Licenses and Permit Management',
            'group' => null,
            'icon' => 'bi bi-shield',
            'img' => 'licenses.png',
            'src' => 'https://img.icons8.com/external-good-lines-kalash/32/external-card-banking-and-money-good-lines-kalash.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 5,
            'code' => 'CM-01',
            'name' => 'Clearance',
            'description' => 'Clearance Management',
            'group' => null,
            'icon' => 'bi bi-file-earmark',
            'img' => 'clearance.png',
            'src' => 'https://img.icons8.com/3d-sugary/100/document-14.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 6,
            'code' => 'MM-01',
            'name' => 'Maintenance',
            'description' => 'Maintenance Management',
            'group' => null,
            'icon' => 'bi bi-wrench',
            'img' => 'maintenance.png',
            'src' => 'https://img.icons8.com/external-wanicon-lineal-color-wanicon/64/external-wrench-construction-wanicon-lineal-color-wanicon.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 7,
            'code' => 'EM-01',
            'name' => 'Employees',
            'description' => 'Employee Management',
            'group' => null,
            'icon' => 'bi bi-person',
            'img' => 'employees.png',
            'src' => 'https://img.icons8.com/external-filled-outline-wichaiwi/64/external-Employee-business-filled-outline-wichaiwi.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-01',
            'name' => 'Reports',
            'description' => 'Reports Management',
            'group' => null,
            'icon' => 'bi bi-bar-chart',
            'img' => 'reports.png',
            'src' => 'https://img.icons8.com/fluency/48/pie-chart-report-script.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-02',
            'name' => 'Asset Listing Report',
            'description' => 'Select parameters for asset listing report. Filter by date range, category, status and location.',
            'group' => null,
            'icon' => 'bi bi-file-earmark',
            'img' => null,
            'src' => null,
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-03',
            'name' => 'Odometer Readings Report',
            'description' => 'Track vehicle usage with detailed odometer readings by date range.',
            'group' => null,
            'icon' => 'bi bi-rulers',
            'img' => null,
            'src' => null,
            'sequence' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-04',
            'name' => 'Maintenance History Report',
            'description' => 'View maintenance records with cost analysis and schedules.',
            'group' => null,
            'icon' => 'bi bi-wrench',
            'img' => null,
            'src' => null,
            'sequence' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-05',
            'name' => 'Employee Listing Report',
            'description' => 'Generate employee listing per location, status and date range.',
            'group' => null,
            'icon' => 'bi bi-person',
            'img' => null,
            'src' => null,
            'sequence' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-06',
            'name' => 'Supplies Summary Report',
            'description' => 'Generate supplies balance summary with filtering by category and supplier.',
            'group' => null,
            'icon' => 'bi bi-cart',
            'img' => null,
            'src' => null,
            'sequence' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-07',
            'name' => 'Supplies Receiving Report',
            'description' => 'Generate detailed and summary supplies receiving report. Filtered by supplier and date range.',
            'group' => null,
            'icon' => 'bi bi-truck',
            'img' => null,
            'src' => null,
            'sequence' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-08',
            'name' => 'Supplies Issuance Report',
            'description' => 'Generate detailed and summary supplies issuance report. Filtered by location, date range and status.',
            'group' => null,
            'icon' => 'bi bi-box',
            'img' => null,
            'src' => null,
            'sequence' => 7,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-09',
            'name' => 'Duty Detail Order Report',
            'description' => 'Generate duty detail order report with location filtering and date range.',
            'group' => null,
            'icon' => 'bi bi-list',
            'img' => null,
            'src' => null,
            'sequence' => 8,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 8,
            'code' => 'REP-10',
            'name' => 'Budget Request Report',
            'description' => 'Generate a report of all budget requests with filtering options.',
            'group' => null,
            'icon' => 'bi bi-wallet',
            'img' => null,
            'src' => null,
            'sequence' => 9,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-01',
            'name' => 'Setup',
            'description' => 'System Settings and Setup',
            'group' => null,
            'icon' => 'bi bi-gear',
            'img' => 'settings.png',
            'src' => 'https://img.icons8.com/bubbles/100/settings.png',
            'sequence' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-02',
            'name' => 'Cluster',
            'description' => 'Setup and manage location cluster or region.',
            'group' => null,
            'icon' => 'bi bi-collection',
            'img' => null,
            'src' => null,
            'sequence' => 2,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-03',
            'name' => 'Location',
            'description' => 'Setup and manage different asset locations or departments.',
            'group' => null,
            'icon' => 'bi bi-geo-alt',
            'img' => null,
            'src' => null,
            'sequence' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-04',
            'name' => 'Asset Categories',
            'description' => 'Setup and manage different asset categories.',
            'group' => null,
            'icon' => 'bi bi-folder',
            'img' => null,
            'src' => null,
            'sequence' => 4,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-05',
            'name' => 'License Types',
            'description' => 'Setup and manage different license types.',
            'group' => null,
            'icon' => 'bi bi-key',
            'img' => null,
            'src' => null,
            'sequence' => 5,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-06',
            'name' => 'Document Types',
            'description' => 'Setup and manage different document types.',
            'group' => null,
            'icon' => 'bi bi-file-earmark',
            'img' => null,
            'src' => null,
            'sequence' => 6,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-07',
            'name' => 'Government IDs',
            'description' => 'Setup and manage different government ids',
            'group' => null,
            'icon' => 'bi bi-person-vcard',
            'img' => null,
            'src' => null,
            'sequence' => 7,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-08',
            'name' => 'Supplies Categories',
            'description' => 'Setup and manage different supplies categories.',
            'group' => null,
            'icon' => 'bi bi-cart',
            'img' => null,
            'src' => null,
            'sequence' => 8,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-09',
            'name' => 'Supplies Units',
            'description' => 'Setup and manage different units of measure.',
            'group' => null,
            'icon' => 'bi bi-rulers',
            'img' => null,
            'src' => null,
            'sequence' => 9,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-10',
            'name' => 'Suppliers',
            'description' => 'Setup and manage different suppliers.',
            'group' => null,
            'icon' => 'bi bi-truck',
            'img' => null,
            'src' => null,
            'sequence' => 10,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-11',
            'name' => 'DDO',
            'description' => 'Setup and manage DDO location.',
            'group' => null,
            'icon' => 'bi bi-geo-alt',
            'img' => null,
            'src' => null,
            'sequence' => 11,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-12',
            'name' => 'Clearance Approval',
            'description' => 'Setup and manage clearance approvals.',
            'group' => null,
            'icon' => 'bi bi-check-circle',
            'img' => null,
            'src' => null,
            'sequence' => 12,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-13',
            'name' => 'Budget Request Approval',
            'description' => 'Setup and manage budget request approvals.',
            'group' => null,
            'icon' => 'bi bi-wallet',
            'img' => null,
            'src' => null,
            'sequence' => 13,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-14',
            'name' => 'User Management',
            'description' => 'Setup and manage AIMS users.',
            'group' => null,
            'icon' => 'bi bi-person',
            'img' => null,
            'src' => null,
            'sequence' => 14,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('sub_modules')->insert([
            'module_id' => 9,
            'code' => 'SET-15',
            'name' => 'User Roles and Permissions',
            'description' => 'Setup and manage user roles and permissions.',
            'group' => null,
            'icon' => 'bi bi-shield',
            'img' => null,
            'src' => null,
            'sequence' => 15,
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
        Schema::dropIfExists('sub_modules');
    }
};

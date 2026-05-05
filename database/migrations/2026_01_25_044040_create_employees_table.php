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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 20)->unique();
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->integer('gender')->default(1); // 1: Male, 2: Female
            $table->integer('marital_status')->default(1); // 1: Single, 2: Married, 3: Widowed
            $table->date('date_of_birth')->nullable();
            $table->string('email', 30)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('position', 60)->nullable();

            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->integer('employment_type')->default(1); // 1: Regular, 2: Contractual, 3: Probationary
            $table->decimal('monthly_salary', 15, 2)->nullable();
            $table->decimal('daily_rate', 15, 2)->nullable();
            $table->decimal('sss_rate', 15, 2)->nullable();
            $table->decimal('philhealth_rate', 10, 2)->nullable();
            $table->decimal('pagibig_rate', 10, 2)->nullable();

            $table->string('address', 255)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('postal_code', 20)->nullable();

            $table->string('highest_education', 150)->nullable();
            $table->string('school', 150)->nullable();
            $table->string('course', 150)->nullable();
            $table->string('year_attended', 50)->nullable();

            $table->string('emergency_contact', 50)->nullable();
            $table->string('emergency_phone', 15)->nullable();

            $table->integer('status')->default(1); // 0: Inactive, 1: Active, 2: On Leave, 3: Resigned, 4: Retired, 5: Terminated, 6: AWOL, 7: Deceased, 8: Dropped, 9: Labor, 10: Floating
            $table->string('photo_path')->nullable();

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
        Schema::dropIfExists('employees');
    }
};

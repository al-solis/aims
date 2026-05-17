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
        Schema::create('budget_headers', function (Blueprint $table) {
            $table->id();
            $table->string('apv_no', 50)->unique();

            $table->unsignedBigInteger('requested_by');
            $table->foreign('requested_by')->references('id')->on('users');

            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');

            $table->text('purpose');
            $table->text('remarks')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->integer('status')->default(0); // 0 = pending, 1 = submitted, 2 = approved, 3 = rejected, 4 = cancelled
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->foreign('approver_id')->references('id')->on('users');
            $table->text('approver_remarks')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        DB::table('numseqs')->where('name', 'BUDGET')->delete();

        DB::table('numseqs')->insert([
            'name' => 'BUDGET',
            'prefix' => 'R',
            'month' => null,
            'year' => null,
            'current_number' => 0,
            'number_length' => 9,
            'created_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_headers');
    }
};

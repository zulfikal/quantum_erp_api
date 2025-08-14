<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index(); // the local date for the attendance

            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();

            $table->enum('clock_in_method', ['web','mobile','biometric','kiosk','manual'])->nullable();
            $table->enum('clock_out_method', ['web','mobile','biometric','kiosk','manual'])->nullable();

            // store lat/lng or use POINT if using MySQL spatial types
            $table->decimal('clock_in_lat', 10, 7)->nullable();
            $table->decimal('clock_in_lng', 10, 7)->nullable();

            $table->enum('status', ['present','absent','on_leave','half_day','pending'])->default('pending');

            $table->unsignedInteger('worked_seconds')->default(0);
            $table->unsignedInteger('total_break_seconds')->default(0);

            $table->string('device_id')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->string('ip_address', 45)->nullable(); // IPv6-ready

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'date']); // ensure one row per employee/day
            $table->index(['employee_id', 'date']); // for quick lookups
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

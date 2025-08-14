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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('claim_type_id')->constrained('claim_types')->cascadeOnDelete();
            $table->double('amount')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'paid'])->default('pending');
            $table->date('request_date');
            $table->string('description')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->string('responded_note')->nullable();
            $table->double('approved_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};

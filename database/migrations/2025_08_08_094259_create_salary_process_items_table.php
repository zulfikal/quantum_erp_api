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
        Schema::create('salary_process_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_process_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamp('date');
            $table->double('basic_amount')->default(0);
            $table->double('allowance_amount')->default(0);
            $table->double('deduction_amount')->default(0);
            $table->double('company_contribution_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_process_items');
    }
};

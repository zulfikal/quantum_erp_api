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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('company_branches')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->text('description');
            $table->string('invoice_number');
            $table->double('total_amount')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('tax_amount')->default(0);
            $table->double('grand_total')->default(0);
            $table->double('shipping_amount')->default(0);
            $table->foreignId('sale_status_id')->constrained('sale_statuses');
            $table->timestamp('invoice_date');
            $table->timestamp('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

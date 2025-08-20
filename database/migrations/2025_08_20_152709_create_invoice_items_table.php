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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->string('name');
            $table->enum('type', ['goods', 'service'])->default('goods');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->double('price')->default(0);
            $table->double('discount')->default(0);
            $table->double('tax_percentage')->default(0);
            $table->double('tax_amount')->default(0);
            $table->integer('quantity')->default(0);
            $table->double('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};

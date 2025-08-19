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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['goods', 'service'])->default('goods');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->double('price');
            $table->integer('stock')->default(0);
            $table->boolean('alert_stock')->default(false);
            $table->integer('alert_stock_threshold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Make SKU unique per company
            $table->unique(['company_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

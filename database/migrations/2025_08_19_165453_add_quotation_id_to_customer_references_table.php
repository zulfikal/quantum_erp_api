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
        Schema::table('customer_references', function (Blueprint $table) {
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_references', function (Blueprint $table) {
            // Drop foreign key first if exists
            try {
                $table->dropForeign('customer_references_quotation_id_foreign');
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore if already dropped
            }

            // Then drop the column if it exists
            if (Schema::hasColumn('customer_references', 'quotation_id')) {
                $table->dropColumn('quotation_id');
            }
        });
    }
};

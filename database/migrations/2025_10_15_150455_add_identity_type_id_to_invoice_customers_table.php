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
        Schema::table('invoice_customers', function (Blueprint $table) {
            $table->foreignId('identity_type_id')->nullable()->constrained('identity_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_customers', function (Blueprint $table) {
            // Drop foreign key first if exists
            try {
                $table->dropForeign('invoice_customers_identity_type_id_foreign');
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore if already dropped
            }

            // Then drop the column if it exists
            if (Schema::hasColumn('invoice_customers', 'identity_type_id')) {
                $table->dropColumn('identity_type_id');
            }
        });
    }
};

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
        Schema::table('entities', function (Blueprint $table) {
            $table->foreignId('identity_type_id')->constrained('identity_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entities', function (Blueprint $table) {
            // Drop foreign key first if exists
            try {
                $table->dropForeign('entities_identity_type_id_foreign');
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore if already dropped
            }

            // Then drop the column if it exists
            if (Schema::hasColumn('entities', 'identity_type_id')) {
                $table->dropColumn('identity_type_id');
            }
        });
    }
};

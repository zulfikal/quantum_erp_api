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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign key first if exists
            try {
                $table->dropForeign('employees_department_id_foreign');
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore if already dropped
            }

            // Then drop the column if it exists
            if (Schema::hasColumn('employees', 'department_id')) {
                $table->dropColumn('department_id');
            }
        });
    }
};

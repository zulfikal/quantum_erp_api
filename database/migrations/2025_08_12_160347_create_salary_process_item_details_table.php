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
        Schema::create('salary_process_item_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('salary_process_item_id')->constrained()->cascadeOnDelete();
            $table->double('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_process_item_details');
    }
};

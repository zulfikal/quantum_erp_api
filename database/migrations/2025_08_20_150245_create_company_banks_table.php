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
        Schema::create('company_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->string('account_number');
            $table->string('holder_name');
            $table->enum('type', ['saving', 'current', 'wallet'])->default('saving');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_banks');
    }
};

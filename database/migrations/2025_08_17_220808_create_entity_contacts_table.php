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
        Schema::create('entity_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->references('id')->on('entities')->cascadeOnDelete();
            $table->enum('type', ['phone', 'mobile', 'email', 'fax', 'other'])->default('phone');
            $table->string('value');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_contacts');
    }
};

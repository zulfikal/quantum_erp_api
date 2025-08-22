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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique();
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->foreignId('company_branch_id')->references('id')->on('company_branches')->cascadeOnDelete();
            $table->foreignId('designation_id')->references('id')->on('designations')->cascadeOnDelete();
            $table->string('nric_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->double('basic_salary')->default(0);
            $table->enum('gender', ['male', 'female']);
            $table->enum('marital_status', ['single', 'married', 'divorced']);
            $table->string('nationality');
            $table->string('religion');
            $table->string('address_1');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country');
            $table->string('register_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

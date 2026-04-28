<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('color')->default('#0d9488');
            $table->integer('days_allowed');
            $table->boolean('is_paid')->default(true);
            $table->boolean('carry_forward')->default(false);
            $table->integer('max_carry_forward')->default(0);
            $table->boolean('requires_document')->default(false);
            $table->integer('min_days_notice')->default(0);
            $table->boolean('applicable_for_probation')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};

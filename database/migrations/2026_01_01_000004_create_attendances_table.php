<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('working_hours', 5, 2)->default(0);
            $table->enum('status', [
                'present', 'absent', 'half_day', 'late', 'early_leave', 
                'on_leave', 'holiday', 'weekend', 'work_from_home'
            ])->default('present');
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable();
            $table->decimal('check_in_lng', 10, 7)->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_regularized')->default(false);
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

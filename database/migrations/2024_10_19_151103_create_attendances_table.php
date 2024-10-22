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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->time('employee_login_time')->nullable();
            $table->time('checkin_from_break')->nullable();
            $table->time('checkout_from_break')->nullable();
            $table->float('remending_hours', 8, 2);
            $table->float('total_hours', 8, 2);
            $table->time('logout_time')->nullable();
            $table->time('login_time')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

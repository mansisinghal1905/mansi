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
        Schema::create('host_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('host_customer_id')->nullable();
            $table->unsignedBigInteger('host_payment_id')->nullable();
            $table->decimal('amount', 10, 2); 
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('host_payment_histories');
    }
};

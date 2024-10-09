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
        Schema::create('quotation_more_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_details_id')->nullable();
            $table->string('quotation_name')->nullable();
            $table->text('short_description')->nullable();
            $table->timestamps();

            $table->foreign('quotation_details_id')->references('id')->on('quotation_details')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_more_details');
    }
};

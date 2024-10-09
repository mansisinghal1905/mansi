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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('invoice_date')->nullable(); 
            $table->date('due_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('date')->nullable();
            $table->decimal('total_amount', 10, 2); // Total amount of the invoice
            $table->decimal('paid_amount', 10, 2)->default(0); // Amount paid so far
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_condition')->nullable();
            $table->string('status')->default('pending'); // Status of the invoice (e.g., pending, paid, overdue)        
            $table->timestamps();
            $table->softDeletes();


            // $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            // $table->foreign('client_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

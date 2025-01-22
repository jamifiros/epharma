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
        Schema::create('stock_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicine_id'); // Foreign key to the stocks table
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->decimal('payout', 8, 2)->nullable()->default(0);
            $table->decimal('balance', 8, 2)->nullable()->default(0);
            $table->timestamps();
        
            // Foreign key constraint
            $table->foreign('medicine_id')->references('id')->on('stocks')->onDelete('cascade');
        });
        
        
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_details');
    }
};

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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('userid'); // Foreign Key
            $table->string('image'); // Prescription image path
            $table->enum('status', [0, 1])->default(0);
            $table->timestamps();
    
            // Foreign Key Constraint
            $table->foreign('userid')->references('id')->on('users')->onDelete('cascade');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};

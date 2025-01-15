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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('prescription_id'); // Foreign Key to prescriptions table
            $table->string('medicine_name'); // Medicine name
            $table->boolean('morning')->default(false); // Morning dose
            $table->boolean('afternoon')->default(false); // Afternoon dose
            $table->boolean('evening')->default(false); // Evening dose
            $table->boolean('night')->default(false); // Night dose
            $table->string('timing');
            $table->integer('total_count')->default(0);
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('prescription_id')->references('id')->on('prescriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};

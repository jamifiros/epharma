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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid'); // Foreign key to users table
            $table->string('guardian_name');
            $table->string('guardian_email');
            $table->string('idproof');
            $table->string('place');
            $table->string('district');
            $table->string('mobile_no');
            $table->timestamps();
        
            $table->foreign('userid')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};

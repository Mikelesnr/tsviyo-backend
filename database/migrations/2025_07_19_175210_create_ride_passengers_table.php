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
        Schema::create('ride_passengers', function (Blueprint $table) {
            $table->id();

            // Foreign key to the rides table
            $table->foreignId('ride_id')
                ->constrained('rides')
                ->onDelete('cascade');

            // Foreign key to the users table
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Optional metadata per passenger instance
            $table->boolean('checked_in')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_passengers');
    }
};

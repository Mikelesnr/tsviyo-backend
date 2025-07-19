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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ride_id')
                ->constrained('rides')
                ->onDelete('cascade');

            $table->foreignId('reviewer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('reviewee_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->tinyInteger('stars')->unsigned(); // 1–5 rating scale

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();

            // Driver (nullable until accepted)
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Coordinates
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_lng', 10, 7);

            // Ride lifecycle & payment status
            $table->enum('status', ['requested', 'accepted', 'in_progress', 'completed', 'canceled'])
                ->default('requested');
            $table->boolean('has_paid')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();

            // Rider profile (required passenger)
            $table->foreignId('rider_id')
                ->constrained('riders')
                ->onDelete('cascade');

            // Driver profile (optional until assigned)
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->onDelete('set null');

            // Coordinates
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_lng', 10, 7);

            // Address strings
            $table->string('pickup_add')->nullable();
            $table->string('dropoff_add')->nullable();

            // Ride metadata
            $table->timestamp('pickup_time')->nullable();
            $table->decimal('fare', 8, 2)->nullable();

            // Ride status
            $table->enum('status', ['requested', 'accepted', 'in_progress', 'completed', 'canceled'])
                ->default('requested');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};

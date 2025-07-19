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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ride_id')
                ->constrained('rides')
                ->onDelete('cascade');

            $table->decimal('amount', 8, 2);
            $table->enum('method', ['cash', 'card', 'mobile']);
            $table->string('transaction_reference')->nullable(); // for mobile or card payments
            $table->boolean('is_confirmed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

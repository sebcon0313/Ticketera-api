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
        Schema::create('booking_seat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_seat_id')->constrained('event_seats')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('price_snapshot', 10, 2)->default(0.00);
            $table->dateTime('hold_expires_at')->nullable();
            $table->enum('status', ['reservado', 'expirado', 'confirmado'])->default('reservado');
            $table->unique(['booking_id', 'event_seat_id'], 'uq_booking_seat');
            $table->index('event_seat_id', 'idx_booking_seat_seat');
            $table->index('status', 'idx_booking_seat_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_seat');
    }
};
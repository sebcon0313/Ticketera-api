<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('booking_seat_id')->constrained('booking_seat')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('ticket_type', ['tarjeta', 'cortesia', 'efectivo'])->default('tarjeta');
            $table->string('qr_code', 255)->unique();
            $table->enum('status', ['emitido', 'usado', 'cancelado'])->default('emitido');
            $table->dateTime('issued_at')->nullable(); // Fecha de entrega
            $table->dateTime('used_at')->nullable(); // fecha de ingreso al evento
            $table->string('pdf_path', 255)->nullable();
            $table->timestamps();

            $table->index('booking_id', 'idx_tickets_booking');
            $table->index('booking_seat_id', 'idx_tickets_booking_seat');
            $table->index('event_id', 'idx_tickets_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
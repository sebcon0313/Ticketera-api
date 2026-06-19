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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->char('reference', 36)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_reference', 255)->nullable();
            $table->enum('status', ['pendiente', 'reservado', 'proceso_pago', 'confirmado', 'pagado', 'cancelado', 'expirado'])
                ->default('pendiente');
            $table->dateTime('reserved_until')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->index('user_id', 'idx_bookings_user');
            $table->index('event_id', 'idx_bookings_event');
            $table->index('status', 'idx_bookings_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('provider', 50);
            $table->string('provider_reference', 255)->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pendiente', 'autorizado', 'pagado', 'fallido', 'rechazado', 'cancelado'])->default('pendiente');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index('booking_id', 'idx_payments_booking');
            $table->index('status', 'idx_payments_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
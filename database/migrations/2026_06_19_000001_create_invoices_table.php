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
        Schema::create('invoices', function (Blueprint $table) {

            $table->id();
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('invoice_number', 50)
                ->unique()
                ->comment('Correlativo de factura');
            $table->string('nit', 55)
                ->nullable()
                ->comment('Número de Identificación Tributaria del cliente');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)
                ->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('status', [
                'emitida',
                'anulada'
            ])->default('emitida');
            $table->string('pdf_path')
                ->nullable();
            $table->dateTime('issued_at');
            $table->dateTime('cancelled_at')
                ->nullable();
            $table->timestamps();
            $table->index('booking_id');
            $table->index('payment_id');
            $table->index('invoice_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
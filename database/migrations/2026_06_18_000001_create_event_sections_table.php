<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_sections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['event_id', 'section_id'], 'uq_event_section');
            $table->index('event_id', 'idx_event_sections_event');
            $table->index('section_id', 'idx_event_sections_section');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_sections');
    }
};

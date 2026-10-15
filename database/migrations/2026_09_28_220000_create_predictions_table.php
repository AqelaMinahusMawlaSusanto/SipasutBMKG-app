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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->date('record_date');
            $table->string('high_tide_time'); // Contoh: "01.00 WIB"
            $table->decimal('high_tide_level', 5, 2); // Contoh: 0.81 (dalam meter)
            $table->string('low_tide_time'); // Contoh: "12.00 WIB"
            $table->decimal('low_tide_level', 5, 2); // Contoh: -0.81 (dalam meter)
            $table->enum('status', ['Aman', 'Waspada', 'Bahaya'])->default('Aman');
            $table->timestamps();

            $table->index(['location_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};

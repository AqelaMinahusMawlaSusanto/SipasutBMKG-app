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
        // 1. Lokasi Pengamatan (5 Titik di Jawa Timur)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // misal: Surabaya Timur, Tanjung Perak, Banyuwangi
            $table->string('code')->unique(); // SBYTIM, SBYBRT, SBYPLB, KAL, BWI
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('description')->nullable();
            $table->string('institution')->default('BMKG Stasiun Meteorologi Maritim Perak Surabaya');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Riwayat Upload Data oleh Admin
        Schema::create('data_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 10)->default('xlsx'); // xlsx, csv, pdf
            $table->unsignedTinyInteger('period_month'); // 1 - 12
            $table->unsignedSmallInteger('period_year'); // 2026
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('total_records')->default(0);
            $table->timestamps();
        });

        // 3. Data Pasang Surut Air Laut Terstruktur (Per Hari & Jam)
        Schema::create('tidal_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained('data_uploads')->cascadeOnDelete();
            $table->date('record_date');
            $table->unsignedTinyInteger('hour'); // 1 - 24 (atau 0 - 23)
            $table->smallInteger('water_level'); // Nilai cm (bisa bernilai negatif terhadap MSL)
            $table->timestamps();

            $table->index(['location_id', 'record_date']);
            $table->unique(['location_id', 'record_date', 'hour']);
        });

        // 4. Kelola Notifikasi / Alert Banner
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'danger', 'success'])->default('info');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('threshold_value')->nullable(); // misal alert jika level > 130 cm
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Login & Aktivitas Log (Untuk Admin)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('action'); // login, logout, failed_login, upload_data, edit_location, etc.
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('tidal_data');
        Schema::dropIfExists('data_uploads');
        Schema::dropIfExists('locations');
    }
};

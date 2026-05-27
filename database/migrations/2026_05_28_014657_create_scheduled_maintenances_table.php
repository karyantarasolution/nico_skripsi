<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('facility_type', ['Fasilitas Umum', 'Fasilitas Sosial', 'Infrastruktur', 'Lainnya'])->default('Fasilitas Umum');
            $table->string('location')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->onDelete('set null');
            $table->date('scheduled_date');
            $table->date('completion_date')->nullable();
            $table->string('status', 30)->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_maintenances');
    }
};

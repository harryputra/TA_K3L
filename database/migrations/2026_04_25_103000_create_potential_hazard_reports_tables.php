<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potential_hazard_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 100)->unique();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('hazard_type', 50);
            $table->string('title', 200);
            $table->string('specific_location')->nullable();
            $table->longText('notes')->nullable();
            $table->enum('status', ['submitted', 'reviewed', 'resolved'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['reported_by', 'status']);
            $table->index(['hazard_type', 'submitted_at']);
        });

        Schema::create('potential_hazard_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_hazard_report_id')->constrained('potential_hazard_reports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potential_hazard_attachments');
        Schema::dropIfExists('potential_hazard_reports');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 100)->unique();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('victim_user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('incident_category_id')->constrained('incident_categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('injury_category_id')->nullable()->constrained('injury_categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('body_part_id')->nullable()->constrained('body_parts')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->enum('severity_level', ['low', 'medium', 'high', 'critical']);
            $table->string('title', 200);
            $table->longText('chronology');
            $table->longText('cause')->nullable();
            $table->longText('initial_action')->nullable();
            $table->longText('impact')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'investigating', 'resolved', 'rejected', 'closed'])->default('draft');
            $table->foreignId('assigned_satgas_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reported_by', 'status']);
            $table->index(['incident_category_id', 'incident_date']);
        });

        Schema::create('incident_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_report_id')->constrained('incident_reports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('incident_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_report_id')->constrained('incident_reports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('change_note')->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('incident_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_report_id')->constrained('incident_reports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->longText('action_taken');
            $table->foreignId('action_owner_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['open', 'in_progress', 'done', 'cancelled'])->default('open');
            $table->string('evidence_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_follow_ups');
        Schema::dropIfExists('incident_status_histories');
        Schema::dropIfExists('incident_attachments');
        Schema::dropIfExists('incident_reports');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropForeign(['incident_category_id']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->foreignId('incident_category_id')->nullable()->change();
            $table->enum('severity_level', ['low', 'medium', 'high', 'critical'])->nullable()->change();
            $table->foreign('incident_category_id')
                ->references('id')
                ->on('incident_categories')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropForeign(['incident_category_id']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->foreignId('incident_category_id')->nullable(false)->change();
            $table->enum('severity_level', ['low', 'medium', 'high', 'critical'])->nullable(false)->change();
            $table->foreign('incident_category_id')
                ->references('id')
                ->on('incident_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};

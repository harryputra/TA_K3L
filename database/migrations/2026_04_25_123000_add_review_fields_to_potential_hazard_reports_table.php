<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('reported_by')->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->after('reviewed_by')->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->timestamp('resolved_at')->nullable()->after('reviewed_at');
            $table->text('response_note')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropConstrainedForeignId('resolved_by');
            $table->dropColumn([
                'reviewed_at',
                'resolved_at',
                'response_note',
            ]);
        });
    }
};

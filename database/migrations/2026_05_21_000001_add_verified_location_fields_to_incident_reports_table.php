<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->foreignId('verified_location_id')->nullable()->after('specific_location')->constrained('locations')->cascadeOnUpdate()->nullOnDelete();
            $table->string('verified_specific_location')->nullable()->after('verified_location_id');
            $table->decimal('verified_latitude', 10, 7)->nullable()->after('verified_specific_location');
            $table->decimal('verified_longitude', 10, 7)->nullable()->after('verified_latitude');
            $table->decimal('verified_location_accuracy', 8, 2)->nullable()->after('verified_longitude');
            $table->foreignId('location_verified_by')->nullable()->after('verified_location_accuracy')->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('location_verified_at')->nullable()->after('location_verified_by');

            $table->index(['verified_latitude', 'verified_longitude']);
        });
    }

    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropIndex(['verified_latitude', 'verified_longitude']);
            $table->dropConstrainedForeignId('location_verified_by');
            $table->dropConstrainedForeignId('verified_location_id');
            $table->dropColumn([
                'verified_specific_location',
                'verified_latitude',
                'verified_longitude',
                'verified_location_accuracy',
                'location_verified_at',
            ]);
        });
    }
};

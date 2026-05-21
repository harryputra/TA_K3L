<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->decimal('location_accuracy', 8, 2)->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->dropColumn('location_accuracy');
        });
    }
};

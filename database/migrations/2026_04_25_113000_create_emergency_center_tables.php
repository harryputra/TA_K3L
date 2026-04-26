<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number', 50);
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('color_class', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('emergency_response_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('first_aid_guides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon', 100)->nullable();
            $table->string('accent_class', 100)->nullable();
            $table->text('summary')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('first_aid_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('first_aid_guide_id')->constrained('first_aid_guides')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('first_aid_actions');
        Schema::dropIfExists('first_aid_guides');
        Schema::dropIfExists('emergency_response_steps');
        Schema::dropIfExists('emergency_contacts');
    }
};

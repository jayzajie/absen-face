<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('face_match_score', 8, 7)->nullable();
            $table->decimal('face_threshold', 8, 7)->nullable();
            $table->string('face_model_version')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['face_match_score', 'face_threshold', 'face_model_version']);
        });
    }
};

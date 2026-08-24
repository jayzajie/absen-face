<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('source')->default('mobile');
            $table->string('device_id')->default('unknown');
            $table->boolean('photo_access_granted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['source', 'device_id', 'photo_access_granted']);
        });
    }
};

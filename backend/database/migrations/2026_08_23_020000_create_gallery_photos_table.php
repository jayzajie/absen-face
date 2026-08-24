<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('employee_name');
            $table->string('file_path');          // path di storage
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->default(0); // bytes
            $table->string('mime_type')->default('image/jpeg');
            $table->timestamp('taken_at')->nullable();           // EXIF date jika ada
            $table->enum('status', ['pending', 'ok', 'flagged'])->default('pending');
            $table->text('flag_note')->nullable();               // catatan teguran dari admin
            $table->timestamp('flagged_at')->nullable();
            $table->string('flagged_by')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'status']);
            $table->index('employee_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
    }
};

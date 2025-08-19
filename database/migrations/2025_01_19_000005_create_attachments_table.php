<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('original_name', 255);
            $table->string('filename', 255); // Stored filename (usually hashed)
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('file_extension', 10);
            $table->string('disk', 50)->default('local'); // Storage disk
            $table->text('description')->nullable();
            
            // Polymorphic relationship - can attach to tasks, projects, comments, etc.
            $table->unsignedBigInteger('attachable_id');
            $table->string('attachable_type', 100); // App\Models\Task, App\Models\Project, etc.
            
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();

            // Foreign key constraints
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('uploaded_by');
            $table->index(['attachable_id', 'attachable_type']);
            $table->index('mime_type');
            $table->index('file_extension');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};

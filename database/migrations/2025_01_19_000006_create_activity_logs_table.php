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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 100); // created, updated, deleted, assigned, completed, etc.
            $table->text('description');
            $table->json('old_values')->nullable(); // Previous values for updates
            $table->json('new_values')->nullable(); // New values for updates
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Polymorphic relationship - can log activity for any model
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 100)->nullable(); // App\Models\Task, App\Models\Project, etc.
            
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for system actions
            $table->timestamp('created_at')->useCurrent();
            $table->string('user_create')->nullable();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index(['subject_id', 'subject_type']);
            $table->index('action');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['subject_id', 'subject_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};

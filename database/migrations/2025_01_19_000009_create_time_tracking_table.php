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
        Schema::create('time_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->text('description')->nullable();
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->decimal('hours', 8, 2)->nullable(); // Calculated or manually entered
            $table->enum('type', ['automatic', 'manual'])->default('manual');
            $table->enum('status', ['running', 'paused', 'stopped'])->default('stopped');
            $table->date('work_date'); // Date when the work was performed
            $table->json('metadata')->nullable(); // Additional tracking data
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->string('user_create')->nullable();
            $table->string('user_update')->nullable();

            // Foreign key constraints
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('task_id');
            $table->index('user_id');
            $table->index('work_date');
            $table->index('status');
            $table->index('type');
            $table->index(['task_id', 'user_id']);
            $table->index(['user_id', 'work_date']);
            $table->index(['task_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_tracking');
    }
};

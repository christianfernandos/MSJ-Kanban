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
        Schema::table('projects', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['created_by']);
            
            // Drop existing indexes
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_by', 'status']);
            
            // Change owner_id to string to match username
            $table->string('owner_id', 20)->change();
            $table->string('created_by', 20)->nullable()->change();
            
            // Add new foreign key constraints to username
            $table->foreign('owner_id')->references('username')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('username')->on('users')->onDelete('set null');
            
            // Add back indexes
            $table->index('owner_id');
            $table->index('created_by');
            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['created_by']);
            
            // Drop indexes
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_by', 'status']);
            
            // Change back to unsignedBigInteger
            $table->unsignedBigInteger('owner_id')->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();
            
            // Add back original foreign key constraints to id
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Add back indexes
            $table->index('owner_id');
            $table->index('created_by');
            $table->index(['created_by', 'status']);
        });
    }
};

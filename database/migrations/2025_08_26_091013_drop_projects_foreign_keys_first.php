<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop all foreign key constraints first
            try {
                $table->dropForeign(['owner_id']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['created_by']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            // Drop indexes
            try {
                $table->dropIndex(['owner_id']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropIndex(['created_by']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropIndex(['created_by', 'status']);
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to rollback
    }
};

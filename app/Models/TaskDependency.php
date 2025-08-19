<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'dependency_type',
        'lag_days',
        'notes',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'lag_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Dependency type constants
    const TYPE_FINISH_TO_START = 'finish_to_start';
    const TYPE_START_TO_START = 'start_to_start';
    const TYPE_FINISH_TO_FINISH = 'finish_to_finish';
    const TYPE_START_TO_FINISH = 'start_to_finish';

    /**
     * Get the dependent task.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Get the task this depends on.
     */
    public function dependsOnTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }

    /**
     * Get dependency type label.
     */
    public function getTypeLabel()
    {
        return match($this->dependency_type) {
            self::TYPE_FINISH_TO_START => 'Finish to Start',
            self::TYPE_START_TO_START => 'Start to Start',
            self::TYPE_FINISH_TO_FINISH => 'Finish to Finish',
            self::TYPE_START_TO_FINISH => 'Start to Finish',
            default => 'Unknown'
        };
    }
}

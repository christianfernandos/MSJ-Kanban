<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'start_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress',
        'notes',
        'project_id',
        'category_id',
        'assigned_to',
        'created_by',
        'position',
        'board_column',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'progress' => 'integer',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_DONE = 'done';
    const STATUS_CANCELLED = 'cancelled';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the category that owns the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the task comments.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the task attachments.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the task activities.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Get the task dependencies (tasks this task depends on).
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    /**
     * Get the tasks that depend on this task.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    /**
     * Get the time tracking entries for this task.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeTracking::class);
    }

    /**
     * Get task status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_TODO => 'secondary',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_REVIEW => 'warning',
            self::STATUS_DONE => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get task priority badge color.
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'danger',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_LOW => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Check if task is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, [self::STATUS_DONE, self::STATUS_CANCELLED]);
    }

    /**
     * Get total time spent on task.
     */
    public function getTotalTimeSpentAttribute()
    {
        return $this->timeEntries()->sum('hours') ?? 0;
    }

    /**
     * Scope for tasks in a specific status.
     */
    public function scopeInStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tasks assigned to a user.
     */
    public function scopeAssignedTo($query, $username)
    {
        return $query->where('assigned_to', $username);
    }

    /**
     * Scope for overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', [self::STATUS_DONE, self::STATUS_CANCELLED]);
    }

    /**
     * Scope for tasks in a specific board column.
     */
    public function scopeInColumn($query, $column)
    {
        return $query->where('board_column', $column)->orderBy('position');
    }

    /**
     * Move task to a different column/status.
     */
    public function moveToColumn($newColumn, $newPosition = null)
    {
        $oldColumn = $this->board_column;
        $oldPosition = $this->position;

        // Update status based on column
        $statusMap = [
            'todo' => self::STATUS_TODO,
            'in_progress' => self::STATUS_IN_PROGRESS,
            'review' => self::STATUS_REVIEW,
            'done' => self::STATUS_DONE
        ];

        $this->board_column = $newColumn;
        $this->status = $statusMap[$newColumn] ?? $newColumn;
        
        if ($newPosition !== null) {
            $this->position = $newPosition;
        }

        // Set completed_at if moving to done
        if ($newColumn === 'done' && !$this->completed_at) {
            $this->completed_at = now();
            $this->progress = 100;
        } elseif ($newColumn !== 'done') {
            $this->completed_at = null;
            if ($this->progress === 100) {
                $this->progress = 90; // Reset from 100% if moved back
            }
        }

        $this->save();

        // Log the activity
        ActivityLog::log(
            'move_task',
            "Moved task from {$oldColumn} to {$newColumn}",
            $this->id,
            self::class,
            ['column' => $oldColumn, 'position' => $oldPosition],
            ['column' => $newColumn, 'position' => $this->position]
        );

        return $this;
    }
}

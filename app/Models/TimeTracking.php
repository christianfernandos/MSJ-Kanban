<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeTracking extends Model
{
    use HasFactory;

    protected $table = 'time_tracking';

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'start_time',
        'end_time',
        'hours',
        'type',
        'status',
        'work_date',
        'metadata',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'hours' => 'decimal:2',
        'work_date' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Type constants
    const TYPE_AUTOMATIC = 'automatic';
    const TYPE_MANUAL = 'manual';

    // Status constants
    const STATUS_RUNNING = 'running';
    const STATUS_PAUSED = 'paused';
    const STATUS_STOPPED = 'stopped';

    /**
     * Get the task that owns the time entry.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who logged the time.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'username');
    }

    /**
     * Calculate hours from start and end time.
     */
    public function calculateHours()
    {
        if ($this->start_time && $this->end_time) {
            $this->hours = $this->start_time->diffInHours($this->end_time, true);
            return $this->hours;
        }
        return 0;
    }

    /**
     * Start a timer.
     */
    public function start()
    {
        $this->start_time = now();
        $this->status = self::STATUS_RUNNING;
        $this->save();
        return $this;
    }

    /**
     * Stop a timer.
     */
    public function stop()
    {
        $this->end_time = now();
        $this->status = self::STATUS_STOPPED;
        $this->calculateHours();
        $this->save();
        return $this;
    }

    /**
     * Pause a timer.
     */
    public function pause()
    {
        $this->status = self::STATUS_PAUSED;
        $this->save();
        return $this;
    }

    /**
     * Resume a timer.
     */
    public function resume()
    {
        $this->status = self::STATUS_RUNNING;
        $this->save();
        return $this;
    }

    /**
     * Scope for running timers.
     */
    public function scopeRunning($query)
    {
        return $query->where('status', self::STATUS_RUNNING);
    }

    /**
     * Scope for entries by user.
     */
    public function scopeByUser($query, $username)
    {
        return $query->where('user_id', $username);
    }

    /**
     * Scope for entries on a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('work_date', $date);
    }
}

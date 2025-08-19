<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'start_date',
        'end_date',
        'budget',
        'color',
        'owner_id',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_HOLD = 'on_hold';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the project owner.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the project tasks.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the project members.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
                    ->withPivot(['role', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get the project member records.
     */
    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Get the project activities.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
                    ->where('subject_type', self::class);
    }

    /**
     * Get tasks by status for Kanban board.
     */
    public function getTasksByStatus()
    {
        return $this->tasks()
                    ->with(['assignee', 'category', 'comments', 'attachments'])
                    ->orderBy('position')
                    ->get()
                    ->groupBy('status');
    }

    /**
     * Get project progress percentage.
     */
    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'done')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    /**
     * Check if user has access to this project.
     */
    public function hasAccess($username)
    {
        // Get user by username
        $user = \App\Models\User::where('username', $username)->first();
        if (!$user) {
            return false;
        }

        // Owner always has access
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Check if user is a member
        return $this->members()->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * Get project status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'primary',
            self::STATUS_ON_HOLD => 'warning',
            self::STATUS_INACTIVE => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get project priority badge color.
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
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for projects owned by user.
     */
    public function scopeOwnedBy($query, $username)
    {
        return $query->where('owner_id', $username);
    }

    /**
     * Scope for projects where user is a member.
     */
    public function scopeAccessibleBy($query, $username)
    {
        return $query->where(function($q) use ($username) {
            $q->where('owner_id', $username)
              ->orWhereHas('members', function($memberQuery) use ($username) {
                  $memberQuery->where('user_id', $username)
                             ->where('status', 'active');
              });
        });
    }
}

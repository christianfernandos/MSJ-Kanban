<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    
    // Disable automatic timestamps since the table may not have updated_at
    public $timestamps = false;

    protected $fillable = [
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'subject_id',
        'subject_type',
        'user_id',
        'user_create',
        'created_at'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    // Action constants
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_ASSIGN = 'assign';
    const ACTION_COMPLETE = 'complete';
    const ACTION_MOVE = 'move';
    const ACTION_COMMENT = 'comment';
    const ACTION_ATTACH = 'attach';

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the subject model.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity.
     */
    public static function log($action, $description, $subjectId = null, $subjectType = null, $oldValues = null, $newValues = null)
    {
        // Get current user ID from session username
        $user = \App\Models\User::where('username', session('username'))->first();
        $userId = $user ? $user->id : null;

        return static::create([
            'action' => $action,
            'description' => $description,
            'subject_id' => $subjectId,
            'subject_type' => $subjectType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => $userId,
            'user_create' => session('username')
        ]);
    }

    /**
     * Get activity icon based on action.
     */
    public function getIconAttribute()
    {
        return match($this->action) {
            self::ACTION_CREATE => 'fas fa-plus text-success',
            self::ACTION_UPDATE => 'fas fa-edit text-primary',
            self::ACTION_DELETE => 'fas fa-trash text-danger',
            self::ACTION_ASSIGN => 'fas fa-user-plus text-info',
            self::ACTION_COMPLETE => 'fas fa-check text-success',
            self::ACTION_MOVE => 'fas fa-arrows-alt text-warning',
            self::ACTION_COMMENT => 'fas fa-comment text-primary',
            self::ACTION_ATTACH => 'fas fa-paperclip text-secondary',
            default => 'fas fa-info-circle text-secondary'
        };
    }

    /**
     * Get activity color based on action.
     */
    public function getColorAttribute()
    {
        return match($this->action) {
            self::ACTION_CREATE => 'success',
            self::ACTION_UPDATE => 'primary',
            self::ACTION_DELETE => 'danger',
            self::ACTION_ASSIGN => 'info',
            self::ACTION_COMPLETE => 'success',
            self::ACTION_MOVE => 'warning',
            self::ACTION_COMMENT => 'primary',
            self::ACTION_ATTACH => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Scope for recent activities.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for activities by user.
     */
    public function scopeByUser($query, $username)
    {
        return $query->where('user_id', $username);
    }

    /**
     * Scope for activities on a specific subject.
     */
    public function scopeForSubject($query, $subjectType, $subjectId = null)
    {
        $query = $query->where('subject_type', $subjectType);
        
        if ($subjectId) {
            $query = $query->where('subject_id', $subjectId);
        }
        
        return $query;
    }
}

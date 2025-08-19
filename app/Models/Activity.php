<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // This model maps to activity_logs table for backward compatibility
    protected $table = 'activity_logs';

    protected $fillable = [
        'action',
        'description',
        'user_id',
        'subject_id',
        'subject_type',
        'user_create'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Action constants
    const ACTION_CREATE_CARD = 'create_card';
    const ACTION_UPDATE_CARD = 'update_card';
    const ACTION_DELETE_CARD = 'delete_card';
    const ACTION_MOVE_CARD = 'move_card';

    /**
     * Log an activity.
     */
    public static function log($username, $action, $description, $subjectId = null, $cardId = null)
    {
        return static::create([
            'action' => $action,
            'description' => $description,
            'user_id' => $username,
            'subject_id' => $subjectId,
            'subject_type' => $cardId ? Task::class : Project::class,
            'user_create' => $username
        ]);
    }
}

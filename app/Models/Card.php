<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;

    // This model maps to the tasks table for backward compatibility
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
        'board_id',
        'list_id',
        'order_index',
        'position',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'due_date' => 'date',
        'order_index' => 'integer',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the board that owns the card.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    /**
     * Get the list that owns the card.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }

    /**
     * Get the user assigned to the card.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'username');
    }

    /**
     * Get the user who created the card.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    /**
     * Scope for cards in a specific status.
     */
    public function scopeInStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for cards assigned to a user.
     */
    public function scopeAssignedTo($query, $username)
    {
        return $query->where('assigned_to', $username);
    }

    /**
     * Get card status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'todo' => 'secondary',
            'in_progress' => 'primary',
            'review' => 'warning',
            'done' => 'success',
            default => 'secondary'
        };
    }
}

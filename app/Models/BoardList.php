<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardList extends Model
{
    use HasFactory;

    // This model represents the columns in a Kanban board
    // For now, we'll use a simple approach with predefined lists
    protected $table = 'board_lists';

    protected $fillable = [
        'name',
        'color',
        'position',
        'board_id',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Color constants
    const COLOR_TODO = '#dc3545';
    const COLOR_PROGRESS = '#ffc107';
    const COLOR_DONE = '#28a745';

    /**
     * Get the board that owns the list.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    /**
     * Get the cards in this list.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'list_id');
    }

    /**
     * Scope for ordered lists.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}

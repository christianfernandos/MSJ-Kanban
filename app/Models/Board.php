<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Board extends Model
{
    use HasFactory;

    // This model maps to the projects table for backward compatibility
    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'year',
        'department',
        'status',
        'created_by',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_COMPLETED = 'completed';

    // Department constants
    const DEPT_PURCHASING = 'purchasing';
    const DEPT_MARKETING = 'marketing';

    /**
     * Get the board creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'username');
    }

    /**
     * Get the board lists.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(BoardList::class, 'board_id');
    }

    /**
     * Get the board cards through tasks.
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class, 'board_id');
    }

    /**
     * Get recently viewed boards.
     */
    public static function getRecentlyViewed($limit = 6)
    {
        // For now, return recent boards. In a real implementation, 
        // you'd track view history in a separate table
        return static::where('status', self::STATUS_ACTIVE)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get boards owned by user.
     */
    public static function getMyBoards($username, $limit = 6)
    {
        return static::where('created_by', $username)
                    ->where('status', self::STATUS_ACTIVE)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get boards shared with user.
     */
    public static function getSharedWithUser($username, $limit = 6)
    {
        // For now, return all boards not owned by user
        // In a real implementation, you'd check project_members table
        return static::where('created_by', '!=', $username)
                    ->where('status', self::STATUS_ACTIVE)
                    ->orderBy('updated_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Check if user has access to this board.
     */
    public function hasAccess($username)
    {
        // Owner always has access
        if ($this->created_by === $username) {
            return true;
        }

        // For now, allow access to all active boards
        // In a real implementation, check project_members table
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Update last viewed timestamp.
     */
    public function updateLastViewed()
    {
        $this->touch();
    }

    /**
     * Add a list to the board.
     */
    public function addList($name, $color = '#6c757d')
    {
        $position = $this->lists()->max('position') + 1;
        
        return $this->lists()->create([
            'name' => $name,
            'color' => $color,
            'position' => $position,
            'user_create' => session('username')
        ]);
    }

    /**
     * Get board structure with lists and cards.
     */
    public function getBoardStructure()
    {
        return $this->lists()
                   ->with(['cards' => function($query) {
                       $query->orderBy('position');
                   }])
                   ->orderBy('position')
                   ->get();
    }

    /**
     * Get board color attributes for display.
     */
    public function getColorStartAttribute()
    {
        // Generate colors based on department
        return match($this->department) {
            self::DEPT_PURCHASING => '#667eea',
            self::DEPT_MARKETING => '#f093fb',
            default => '#4facfe'
        };
    }

    public function getColorEndAttribute()
    {
        return match($this->department) {
            self::DEPT_PURCHASING => '#764ba2',
            self::DEPT_MARKETING => '#f5576c',
            default => '#00f2fe'
        };
    }
}

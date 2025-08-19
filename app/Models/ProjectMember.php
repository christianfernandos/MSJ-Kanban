<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $table = 'project_members';

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'status',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Role constants
    const ROLE_OWNER = 'owner';
    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';
    const ROLE_VIEWER = 'viewer';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * Get the project that this member belongs to
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user for this project member
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for specific role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if member has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if member is owner
     */
    public function isOwner()
    {
        return $this->hasRole(self::ROLE_OWNER);
    }

    /**
     * Check if member is admin
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if member can manage project
     */
    public function canManage()
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_ADMIN]);
    }

    /**
     * Check if member can edit tasks
     */
    public function canEditTasks()
    {
        return in_array($this->role, [self::ROLE_OWNER, self::ROLE_ADMIN, self::ROLE_MEMBER]);
    }

    /**
     * Check if member can view project
     */
    public function canView()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get member's full name
     */
    public function getFullNameAttribute()
    {
        if ($this->user) {
            return trim($this->user->firstname . ' ' . $this->user->lastname) ?: $this->user->username;
        }
        return 'Unknown User';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        return ucfirst($this->role);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        return ucfirst($this->status);
    }
}
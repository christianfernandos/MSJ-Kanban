<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'isactive',
        'order_index',
        'user_create',
        'user_update'
    ];

    protected $casts = [
        'isactive' => 'boolean',
        'order_index' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the tasks for the category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('isactive', 1);
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index')->orderBy('name');
    }

    /**
     * Get the category badge HTML.
     */
    public function getBadgeAttribute()
    {
        $icon = $this->icon ? "<i class='{$this->icon} me-1'></i>" : '';
        return "<span class='badge' style='background-color: {$this->color}; color: white;'>{$icon}{$this->name}</span>";
    }
}

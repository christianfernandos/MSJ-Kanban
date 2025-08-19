<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    use HasFactory;

    /**
     * Get card status count for dashboard.
     */
    public function getCardStatusCount()
    {
        return [
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'done' => Task::where('status', 'done')->count(),
            'total' => Task::count()
        ];
    }

    /**
     * Get recent activities for dashboard.
     */
    public function getRecentActivities($limit = 10)
    {
        return ActivityLog::with(['user', 'subject'])
                         ->orderBy('created_at', 'desc')
                         ->limit($limit)
                         ->get();
    }

    /**
     * Get reminders for dashboard.
     */
    public function getReminders($limit = 5)
    {
        return Task::with(['assignee', 'project'])
                  ->where('due_date', '<=', now()->addDays(3))
                  ->where('status', '!=', 'done')
                  ->orderBy('due_date')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Get project statistics.
     */
    public function getProjectStats()
    {
        return [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'overdue_tasks' => Task::where('due_date', '<', now())
                                  ->whereNotIn('status', ['done', 'cancelled'])
                                  ->count()
        ];
    }

    /**
     * Get user workload.
     */
    public function getUserWorkload($username)
    {
        return [
            'assigned_tasks' => Task::where('assigned_to', $username)
                                   ->whereNotIn('status', ['done', 'cancelled'])
                                   ->count(),
            'overdue_tasks' => Task::where('assigned_to', $username)
                                  ->where('due_date', '<', now())
                                  ->whereNotIn('status', ['done', 'cancelled'])
                                  ->count(),
            'completed_this_week' => Task::where('assigned_to', $username)
                                        ->where('status', 'done')
                                        ->where('completed_at', '>=', now()->startOfWeek())
                                        ->count()
        ];
    }
}

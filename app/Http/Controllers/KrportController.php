<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\TimeTracking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class KrportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($data)
    {
        // function helper
        $data['format'] = new Format_Helper;

        // Get user login information and rules
        $data['user_login'] = User::where('username', session('username'))->first();

        // Check if user is logged in
        if (!$data['user_login']) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        $users_rules = array_map('trim', explode(',', $data['user_login']->idroles ?? ''));
        $data['users_rules'] = $users_rules;

        // Get system app configuration
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();

        // Query gmenu for navigation
        $data['gmenu'] = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->whereIn('sys_auth.idroles', $users_rules)
            ->where('sys_gmenu.isactive', '1')
            ->select('sys_gmenu.*')
            ->distinct()
            ->orderBy('urut')
            ->get();

        // Set title group and menu URL
        $data['title_group'] = 'Kanban Reports';
        $data['url_menu'] = 'krport';

        // Get dashboard statistics
        $data['total_projects'] = Project::count();
        $data['active_projects'] = Project::where('status', 'active')->count();
        $data['total_tasks'] = Task::count();
        $data['completed_tasks'] = Task::where('status', 'done')->count();
        $data['total_users'] = User::where('isactive', 1)->count();

        // Task status distribution
        $data['task_status_stats'] = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                return [ucfirst(str_replace('_', ' ', $item->status)) => $item->count];
            });

        // Task priority distribution
        $data['task_priority_stats'] = Task::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function($item) {
                return [ucfirst($item->priority) => $item->count];
            });

        // Projects by department
        $data['project_department_stats'] = Project::select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->department => $item->count];
            });

        // Recent activities
        $data['recent_activities'] = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Top performers (users with most completed tasks)
        $data['top_performers'] = Task::select('assigned_to', DB::raw('count(*) as completed_tasks'))
            ->where('status', 'done')
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->orderBy('completed_tasks', 'desc')
            ->limit(5)
            ->with('assignee')
            ->get();

        return view($data['url'], $data);
    }

    /**
     * Project Progress Report
     */
    public function projectProgress($data)
    {
        // Setup navigation data
        $data = $this->setupNavigationData($data);

        // Get projects with task statistics
        $projects = Project::with(['tasks' => function($query) {
                $query->select('project_id', 'status', DB::raw('count(*) as count'))
                      ->groupBy('project_id', 'status');
            }])
            ->get()
            ->map(function($project) {
                $taskStats = $project->tasks->groupBy('status');
                
                $totalTasks = $project->tasks()->count();
                $completedTasks = $project->tasks()->where('status', 'done')->count();
                $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

                return (object)[
                    'id' => $project->id,
                    'name' => $project->name,
                    'department' => $project->department,
                    'status' => $project->status,
                    'total_tasks' => $totalTasks,
                    'todo_tasks' => $project->tasks()->where('status', 'todo')->count(),
                    'in_progress_tasks' => $project->tasks()->where('status', 'in_progress')->count(),
                    'review_tasks' => $project->tasks()->where('status', 'review')->count(),
                    'completed_tasks' => $completedTasks,
                    'progress_percentage' => $progressPercentage,
                    'created_at' => $project->created_at
                ];
            });

        $data['projects'] = $projects;

        return view($data['url'], $data);
    }

    /**
     * User Performance Report
     */
    public function userPerformance($data)
    {
        // Setup navigation data
        $data = $this->setupNavigationData($data);

        // Get users with task statistics
        $users = User::where('isactive', 1)
            ->with(['assignedTasks'])
            ->get()
            ->map(function($user) {
                $totalTasks = $user->assignedTasks->count();
                $completedTasks = $user->assignedTasks->where('status', 'done')->count();
                $inProgressTasks = $user->assignedTasks->where('status', 'in_progress')->count();
                $overdueTasks = $user->assignedTasks->where('due_date', '<', now())
                    ->whereNotIn('status', ['done'])->count();

                // Get time tracking data
                $totalTimeTracked = TimeTracking::where('user_id', $user->id)
                    ->whereNotNull('end_time')
                    ->get()
                    ->sum(function($entry) {
                        return Carbon::parse($entry->end_time)->diffInMinutes(Carbon::parse($entry->start_time));
                    });

                return (object)[
                    'id' => $user->id,
                    'name' => trim($user->firstname . ' ' . $user->lastname) ?: $user->username,
                    'username' => $user->username,
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'in_progress_tasks' => $inProgressTasks,
                    'overdue_tasks' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
                    'total_time_hours' => round($totalTimeTracked / 60, 2)
                ];
            })
            ->sortByDesc('completion_rate');

        $data['users'] = $users;

        return view($data['url'], $data);
    }

    /**
     * Time Tracking Report
     */
    public function timeTracking($data)
    {
        // Setup navigation data
        $data = $this->setupNavigationData($data);

        // Get date range from request or default to current month
        $startDate = request('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $data['start_date'] = $startDate;
        $data['end_date'] = $endDate;

        // Get time tracking entries for the period
        $timeEntries = TimeTracking::with(['task.project', 'user'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->whereNotNull('end_time')
            ->get();

        // Group by user
        $userTimeStats = $timeEntries->groupBy('user_id')->map(function($entries, $userId) {
            $user = $entries->first()->user;
            $totalMinutes = $entries->sum(function($entry) {
                return Carbon::parse($entry->end_time)->diffInMinutes(Carbon::parse($entry->start_time));
            });

            return (object)[
                'user_name' => $user ? ($user->firstname . ' ' . $user->lastname) : 'Unknown',
                'total_hours' => round($totalMinutes / 60, 2),
                'total_entries' => $entries->count(),
                'avg_session_hours' => $entries->count() > 0 ? round(($totalMinutes / 60) / $entries->count(), 2) : 0
            ];
        })->sortByDesc('total_hours');

        // Group by project
        $projectTimeStats = $timeEntries->groupBy('task.project_id')->map(function($entries) {
            $project = $entries->first()->task->project ?? null;
            $totalMinutes = $entries->sum(function($entry) {
                return Carbon::parse($entry->end_time)->diffInMinutes(Carbon::parse($entry->start_time));
            });

            return (object)[
                'project_name' => $project ? $project->name : 'Unknown',
                'total_hours' => round($totalMinutes / 60, 2),
                'total_entries' => $entries->count()
            ];
        })->sortByDesc('total_hours');

        // Daily time tracking
        $dailyTimeStats = $timeEntries->groupBy(function($entry) {
            return Carbon::parse($entry->start_time)->format('Y-m-d');
        })->map(function($entries, $date) {
            $totalMinutes = $entries->sum(function($entry) {
                return Carbon::parse($entry->end_time)->diffInMinutes(Carbon::parse($entry->start_time));
            });

            return (object)[
                'date' => $date,
                'total_hours' => round($totalMinutes / 60, 2),
                'total_entries' => $entries->count()
            ];
        })->sortBy('date');

        $data['user_time_stats'] = $userTimeStats;
        $data['project_time_stats'] = $projectTimeStats;
        $data['daily_time_stats'] = $dailyTimeStats;

        return view($data['url'], $data);
    }

    /**
     * Task Analysis Report
     */
    public function taskAnalysis($data)
    {
        // Setup navigation data
        $data = $this->setupNavigationData($data);

        // Task completion trends (last 30 days)
        $completionTrends = Task::select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('count(*) as completed_count')
            )
            ->where('status', 'done')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Tasks by category
        $tasksByCategory = Task::with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function($tasks, $categoryName) {
                return (object)[
                    'category' => $categoryName ?: 'Uncategorized',
                    'total_tasks' => $tasks->count(),
                    'completed_tasks' => $tasks->where('status', 'done')->count(),
                    'completion_rate' => $tasks->count() > 0 ? 
                        round(($tasks->where('status', 'done')->count() / $tasks->count()) * 100, 2) : 0
                ];
            })
            ->sortByDesc('total_tasks');

        // Overdue tasks analysis
        $overdueTasks = Task::where('due_date', '<', now())
            ->whereNotIn('status', ['done'])
            ->with(['project', 'assignee', 'category'])
            ->get();

        // Average task completion time
        $completedTasks = Task::where('status', 'done')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        $avgCompletionDays = $completedTasks->avg(function($task) {
            return Carbon::parse($task->created_at)->diffInDays(Carbon::parse($task->updated_at));
        });

        $data['completion_trends'] = $completionTrends;
        $data['tasks_by_category'] = $tasksByCategory;
        $data['overdue_tasks'] = $overdueTasks;
        $data['avg_completion_days'] = round($avgCompletionDays, 1);

        return view($data['url'], $data);
    }

    /**
     * Export report data
     */
    public function export($data)
    {
        try {
            if ($data['authorize']->excel != '1') {
                throw new \Exception('Not Authorized');
            }

            $reportType = request('type', 'summary');
            $filename = 'kanban_report_' . $reportType . '_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($reportType) {
                $file = fopen('php://output', 'w');
                
                switch ($reportType) {
                    case 'projects':
                        $this->exportProjectsReport($file);
                        break;
                    case 'tasks':
                        $this->exportTasksReport($file);
                        break;
                    case 'users':
                        $this->exportUsersReport($file);
                        break;
                    default:
                        $this->exportSummaryReport($file);
                        break;
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return $this->showError($data, $e->getMessage());
        }
    }

    /**
     * Export summary report
     */
    private function exportSummaryReport($file)
    {
        fputcsv($file, ['Kanban Summary Report - ' . date('Y-m-d H:i:s')]);
        fputcsv($file, []);
        
        // Overall statistics
        fputcsv($file, ['Overall Statistics']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Total Projects', Project::count()]);
        fputcsv($file, ['Active Projects', Project::where('status', 'active')->count()]);
        fputcsv($file, ['Total Tasks', Task::count()]);
        fputcsv($file, ['Completed Tasks', Task::where('status', 'done')->count()]);
        fputcsv($file, ['Total Users', User::where('isactive', 1)->count()]);
        fputcsv($file, []);

        // Task status distribution
        fputcsv($file, ['Task Status Distribution']);
        fputcsv($file, ['Status', 'Count']);
        $statusStats = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        foreach ($statusStats as $stat) {
            fputcsv($file, [ucfirst(str_replace('_', ' ', $stat->status)), $stat->count]);
        }
    }

    /**
     * Export projects report
     */
    private function exportProjectsReport($file)
    {
        fputcsv($file, ['Project Progress Report - ' . date('Y-m-d H:i:s')]);
        fputcsv($file, []);
        fputcsv($file, [
            'Project Name',
            'Department', 
            'Status',
            'Total Tasks',
            'Completed Tasks',
            'Progress %',
            'Created Date'
        ]);

        $projects = Project::with('tasks')->get();
        foreach ($projects as $project) {
            $totalTasks = $project->tasks->count();
            $completedTasks = $project->tasks->where('status', 'done')->count();
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            fputcsv($file, [
                $project->name,
                $project->department,
                $project->status,
                $totalTasks,
                $completedTasks,
                $progress . '%',
                $project->created_at->format('Y-m-d')
            ]);
        }
    }

    /**
     * Export tasks report
     */
    private function exportTasksReport($file)
    {
        fputcsv($file, ['Tasks Report - ' . date('Y-m-d H:i:s')]);
        fputcsv($file, []);
        fputcsv($file, [
            'Task Title',
            'Project',
            'Category',
            'Status',
            'Priority',
            'Assigned To',
            'Due Date',
            'Progress %',
            'Created Date'
        ]);

        $tasks = Task::with(['project', 'category', 'assignee'])->get();
        foreach ($tasks as $task) {
            fputcsv($file, [
                $task->title,
                $task->project ? $task->project->name : '-',
                $task->category ? $task->category->name : '-',
                ucfirst(str_replace('_', ' ', $task->status)),
                ucfirst($task->priority),
                $task->assignee ? ($task->assignee->firstname . ' ' . $task->assignee->lastname) : '-',
                $task->due_date ? $task->due_date->format('Y-m-d') : '-',
                $task->progress . '%',
                $task->created_at->format('Y-m-d')
            ]);
        }
    }

    /**
     * Export users report
     */
    private function exportUsersReport($file)
    {
        fputcsv($file, ['User Performance Report - ' . date('Y-m-d H:i:s')]);
        fputcsv($file, []);
        fputcsv($file, [
            'User Name',
            'Username',
            'Total Tasks',
            'Completed Tasks',
            'In Progress Tasks',
            'Completion Rate %',
            'Total Time (Hours)'
        ]);

        $users = User::where('isactive', 1)->with('assignedTasks')->get();
        foreach ($users as $user) {
            $totalTasks = $user->assignedTasks->count();
            $completedTasks = $user->assignedTasks->where('status', 'done')->count();
            $inProgressTasks = $user->assignedTasks->where('status', 'in_progress')->count();
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            // Calculate total time
            $totalTime = TimeTracking::where('user_id', $user->id)
                ->whereNotNull('end_time')
                ->get()
                ->sum(function($entry) {
                    return Carbon::parse($entry->end_time)->diffInMinutes(Carbon::parse($entry->start_time));
                });

            fputcsv($file, [
                trim($user->firstname . ' ' . $user->lastname) ?: $user->username,
                $user->username,
                $totalTasks,
                $completedTasks,
                $inProgressTasks,
                $completionRate . '%',
                round($totalTime / 60, 2)
            ]);
        }
    }

    /**
     * Setup navigation data for views
     */
    private function setupNavigationData($data)
    {
        // Ensure user_login is set
        if (!isset($data['user_login'])) {
            $data['user_login'] = User::where('username', session('username'))->first();
        }

        // Check if user is logged in
        if (!$data['user_login']) {
            throw new \Exception('User not authenticated');
        }

        // Ensure users_rules is set
        if (!isset($data['users_rules'])) {
            $users_rules = array_map('trim', explode(',', $data['user_login']->idroles ?? ''));
            $data['users_rules'] = $users_rules;
        } else {
            $users_rules = $data['users_rules'];
        }

        // Ensure setup_app is set
        if (!isset($data['setup_app'])) {
            $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();
        }

        // Ensure url_menu is set
        if (!isset($data['url_menu'])) {
            $data['url_menu'] = 'krport';
        }

        // Ensure other MSJ framework variables are set
        if (!isset($data['dmenu'])) {
            $data['dmenu'] = 'krport';
        }

        if (!isset($data['gmenuid'])) {
            $data['gmenuid'] = 'kanban';
        }

        if (!isset($data['tabel'])) {
            $data['tabel'] = '-';
        }

        if (!isset($data['jsmenu'])) {
            $data['jsmenu'] = '0';
        }

        // Ensure authorize object is set
        if (!isset($data['authorize'])) {
            $data['authorize'] = (object)[
                'add' => '0',
                'edit' => '0',
                'delete' => '0',
                'approval' => '0',
                'value' => '0',
                'print' => '1',
                'excel' => '1',
                'pdf' => '1'
            ];
        }

        // Ensure gmenu is set
        if (!isset($data['gmenu'])) {
            $data['gmenu'] = DB::table('sys_gmenu')
                ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
                ->whereIn('sys_auth.idroles', $users_rules)
                ->where('sys_gmenu.isactive', '1')
                ->select('sys_gmenu.*')
                ->distinct()
                ->orderBy('urut')
                ->get();
        }

        return $data;
    }

    /**
     * Show error page
     */
    private function showError($data, $message)
    {
        $data = $this->setupNavigationData($data);
        $data['url_menu'] = 'error';
        $data['title_group'] = 'Error';
        $data['title_menu'] = 'Error';
        $data['errorpages'] = $message;
        return view("pages.errorpages", $data);
    }
}
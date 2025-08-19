<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KdashController extends Controller
{
    /**
     * Display the Kanban Dashboard.
     */
    public function index($data)
    {
        // function helper
        $data['format'] = new Format_Helper;

        // Get user login information and rules (required for gmenu query)
        $data['user_login'] = User::where('username', session('username'))->first();

        // Check if user is logged in
        if (!$data['user_login']) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        $users_rules = array_map('trim', explode(',', $data['user_login']->idroles ?? ''));
        $data['users_rules'] = $users_rules;

        // Get system app configuration
        $data['setup_app'] = DB::table('sys_app')->where('isactive', '1')->first();

        // Query gmenu for navigation (required by layout)
        $data['gmenu'] = DB::table('sys_gmenu')
            ->join('sys_auth', 'sys_gmenu.gmenu', '=', 'sys_auth.gmenu')
            ->whereIn('sys_auth.idroles', $users_rules)
            ->where('sys_gmenu.isactive', '1')
            ->select('sys_gmenu.*')
            ->distinct()
            ->orderBy('urut')
            ->get();

        // Set title group and menu URL
        $data['title_group'] = 'Kanban Dashboard';
        $data['url_menu'] = 'kdash';

        // Dashboard Statistics
        $data['total_projects'] = Project::count();
        $data['active_projects'] = Project::where('status', 'active')->count();
        $data['total_tasks'] = Task::count();
        $data['completed_tasks'] = Task::where('status', 'done')->count();
        $data['pending_tasks'] = Task::whereIn('status', ['todo', 'in_progress', 'review'])->count();
        $data['overdue_tasks'] = Task::where('due_date', '<', Carbon::now())
                                    ->whereNotIn('status', ['done'])
                                    ->count();

        // Recent Activities
        $data['recent_activities'] = ActivityLog::with(['user'])
                                               ->orderBy('created_at', 'desc')
                                               ->limit(10)
                                               ->get();

        // Tasks by Status
        $data['tasks_by_status'] = [
            'todo' => Task::where('status', 'todo')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'done' => Task::where('status', 'done')->count()
        ];

        // Tasks by Priority
        $data['tasks_by_priority'] = [
            'low' => Task::where('priority', 'low')->count(),
            'medium' => Task::where('priority', 'medium')->count(),
            'high' => Task::where('priority', 'high')->count(),
            'urgent' => Task::where('priority', 'urgent')->count()
        ];

        // My Tasks (assigned to current user)
        $currentUser = User::where('username', session('username'))->first();
        if ($currentUser) {
            $data['my_tasks'] = Task::where('assigned_to', $currentUser->id)
                                   ->whereNotIn('status', ['done'])
                                   ->with(['project', 'category'])
                                   ->orderBy('due_date', 'asc')
                                   ->limit(5)
                                   ->get();
        } else {
            $data['my_tasks'] = collect();
        }

        // Upcoming Deadlines
        $data['upcoming_deadlines'] = Task::where('due_date', '>=', Carbon::now())
                                          ->where('due_date', '<=', Carbon::now()->addDays(7))
                                          ->whereNotIn('status', ['done'])
                                          ->with(['project', 'assignee'])
                                          ->orderBy('due_date', 'asc')
                                          ->limit(5)
                                          ->get();

        // Project Progress
        $data['project_progress'] = Project::with(['tasks'])
                                          ->where('status', 'active')
                                          ->get()
                                          ->map(function($project) {
                                              $totalTasks = $project->tasks->count();
                                              $completedTasks = $project->tasks->where('status', 'done')->count();
                                              $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
                                              
                                              return [
                                                  'id' => $project->id,
                                                  'name' => $project->name,
                                                  'total_tasks' => $totalTasks,
                                                  'completed_tasks' => $completedTasks,
                                                  'progress' => $progress
                                              ];
                                          })
                                          ->sortByDesc('progress')
                                          ->take(5);

        return view($data['url'], $data);
    }

    /**
     * Get dashboard data for AJAX requests.
     */
    public function getDashboardData()
    {
        try {
            $currentUser = User::where('username', session('username'))->first();
            
            $data = [
                'stats' => [
                    'total_projects' => Project::count(),
                    'active_projects' => Project::where('status', 'active')->count(),
                    'total_tasks' => Task::count(),
                    'completed_tasks' => Task::where('status', 'done')->count(),
                    'pending_tasks' => Task::whereIn('status', ['todo', 'in_progress', 'review'])->count(),
                    'overdue_tasks' => Task::where('due_date', '<', Carbon::now())
                                          ->whereNotIn('status', ['done'])
                                          ->count()
                ],
                'tasks_by_status' => [
                    'todo' => Task::where('status', 'todo')->count(),
                    'in_progress' => Task::where('status', 'in_progress')->count(),
                    'review' => Task::where('status', 'review')->count(),
                    'done' => Task::where('status', 'done')->count()
                ],
                'tasks_by_priority' => [
                    'low' => Task::where('priority', 'low')->count(),
                    'medium' => Task::where('priority', 'medium')->count(),
                    'high' => Task::where('priority', 'high')->count(),
                    'urgent' => Task::where('priority', 'urgent')->count()
                ]
            ];

            if ($currentUser) {
                $data['my_tasks'] = Task::where('assigned_to', $currentUser->id)
                                       ->whereNotIn('status', ['done'])
                                       ->with(['project', 'category'])
                                       ->orderBy('due_date', 'asc')
                                       ->limit(5)
                                       ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
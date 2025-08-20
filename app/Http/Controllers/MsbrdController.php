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
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MsbrdController extends Controller
{
    /**
     * Display a listing of the resource (Kanban Boards List).
     */
    public function index($data)
    {
        // function helper
        $data['format'] = new Format_Helper;
        $syslog = new Function_Helper;

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

        // Get recently viewed projects (last 6)
        $data['recently_viewed'] = Project::where('status', 'active')
                                         ->orderBy('updated_at', 'desc')
                                         ->limit(6)
                                         ->get();

        // Get user's own projects - handle the case where created_by might be null
        try {
            $data['my_boards'] = Project::where(function($query) use ($data) {
                                        $query->where('created_by', $data['user_login']->id)
                                              ->orWhere('owner_id', $data['user_login']->id);
                                    })
                                   ->where('status', 'active')
                                   ->withCount('tasks')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        } catch (\Exception $e) {
            // If there's an issue with created_by column, fall back to owner_id only
            $data['my_boards'] = Project::where('owner_id', $data['user_login']->id)
                                       ->where('status', 'active')
                                       ->withCount('tasks')
                                       ->orderBy('created_at', 'desc')
                                       ->get();
        }

        // Get shared projects (projects not created by current user)
        try {
            $data['shared_boards'] = Project::where('created_by', '!=', $data['user_login']->id)
                                            ->where('owner_id', '!=', $data['user_login']->id)
                                            ->where('status', 'active')
                                            ->withCount('tasks')
                                            ->orderBy('name')
                                            ->limit(12)
                                            ->get();
        } catch (\Exception $e) {
            // If there's an issue, return empty collection
            $data['shared_boards'] = collect();
        }

        // Log access
        if (!Session::has('message')) {
            $syslog->log_insert('V', $data['dmenu'], 'Access Kanban Boards List', '1');
        }

        // Use the boards view
        // $data['url'] = 'kanban.msbrd.boards';

        return view($data['url'], $data);
    }

    /**
     * Display the Kanban board for a specific project.
     */
    public function show($data)
    {
        // function helper
        $data['format'] = new Format_Helper;
        $syslog = new Function_Helper;

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

        // Get the specific project
        try {
            // Try to get project ID from the encrypted parameter
            if (is_numeric($data['idencrypt'])) {
                $projectId = $data['idencrypt'];
            } else {
                $projectId = decrypt($data['idencrypt']);
            }
            $data['current_project'] = Project::findOrFail($projectId);
        } catch (\Exception $e) {
            // If decryption fails or project not found, get the first available project
            $data['current_project'] = Project::where('status', 'active')
                                             ->orderBy('updated_at', 'desc')
                                             ->first();
            
            if (!$data['current_project']) {
                return redirect('/msbrd')->with('error', 'No projects found.');
            }
        }

        // Get all active projects for project selection
        $data['projects'] = Project::where('status', 'active')
                                  ->orderBy('name')
                                  ->get();

        // Get tasks grouped by status for Kanban columns
        $data['tasks_by_status'] = $this->getTasksByStatus($data['current_project']->id);

        // Get categories for task creation
        $data['categories'] = Category::where('is_active', 1)
                                    ->orderBy('name')
                                    ->get();

        // Get all users for task assignment
        $data['all_users'] = User::where('isactive', 1)
                                ->select('id', 'username', 'firstname', 'lastname')
                                ->orderBy('firstname')
                                ->get();

        // Log access
        if (!Session::has('message')) {
            $syslog->log_insert('V', $data['dmenu'], 'Access Kanban Board - ' . $data['current_project']->name, '1');
        }

        // Use the kanban board view
        // $data['url'] = 'kanban.msbrd.show';

        return view($data['url'], $data);
    }

    /**
     * Store a newly created project/board.
     */
    public function store($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            // Get current user
            $currentUser = User::where('username', session('username'))->first();
            if (!$currentUser) {
                return response()->json(['success' => false, 'message' => 'User not found'], 401);
            }

            // Validate and trim description to prevent overflow
            $description = request()->description;
            if (strlen($description) > 255) {
                $description = substr($description, 0, 252) . '...';
            }

            // Create new project/board
            $project = Project::create([
                'name' => request()->name,
                'description' => $description,
                'year' => request()->year ?? date('Y'),
                'department' => request()->department ?? 'general',
                'owner_id' => $currentUser->id,
                'created_by' => $currentUser->id,
                'status' => 'active',
                'user_create' => session('username')
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'create_project',
                'description' => 'Created new project: ' . $project->name,
                'model_type' => Project::class,
                'model_id' => $project->id,
                'user_create' => session('username')
            ]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Board created successfully!',
                    'board' => $project
                ]);
            }

            Session::flash('message', 'Board created successfully!');
            Session::flash('class', 'success');

            return redirect($data['url_menu']);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Create Board Error: ' . $e->getMessage(), '0');
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create board: ' . $e->getMessage()
                ], 500);
            }

            Session::flash('message', 'Failed to create board!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }

    /**
     * Get tasks grouped by status for Kanban board
     */
    private function getTasksByStatus($projectId)
    {
        $tasks = Task::where('project_id', $projectId)
                    ->with(['assignee', 'category', 'creator'])
                    ->orderBy('board_column')
                    ->orderBy('position')
                    ->get();

        return $tasks->groupBy('board_column');
    }

    /**
     * API: Get board data for AJAX requests
     */
    public function api($data)
    {
        $action = $data['idencrypt'] ?? 'get-board';

        switch ($action) {
            case 'get-board':
                return $this->getBoardData();
            case 'create-task':
                return $this->createTask();
            case 'update-task':
                return $this->updateTask();
            case 'move-task':
                return $this->moveTask();
            case 'delete-task':
                return $this->deleteTask();
            case 'get-task':
                return $this->getTask();
            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }
    }

    /**
     * Get board data for AJAX
     */
    private function getBoardData()
    {
        try {
            $projectId = request('project_id', 1);
            $tasksByStatus = $this->getTasksByStatus($projectId);

            $columns = [
                'todo' => [
                    'id' => 'todo',
                    'name' => 'TO DO',
                    'color' => '#dc3545',
                    'tasks' => $tasksByStatus->get('todo', collect())->values()
                ],
                'in_progress' => [
                    'id' => 'in_progress',
                    'name' => 'IN PROGRESS',
                    'color' => '#ffc107',
                    'tasks' => $tasksByStatus->get('in_progress', collect())->values()
                ],
                'review' => [
                    'id' => 'review',
                    'name' => 'REVIEW',
                    'color' => '#17a2b8',
                    'tasks' => $tasksByStatus->get('review', collect())->values()
                ],
                'done' => [
                    'id' => 'done',
                    'name' => 'DONE',
                    'color' => '#28a745',
                    'tasks' => $tasksByStatus->get('done', collect())->values()
                ]
            ];

            return response()->json([
                'success' => true,
                'columns' => $columns
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create new task
     */
    private function createTask()
    {
        try {
            DB::beginTransaction();

            $currentUser = User::where('username', session('username'))->first();
            if (!$currentUser) {
                return response()->json(['error' => 'User not found'], 401);
            }

            $assignedUserId = null;
            if (request('assigned_to')) {
                $assignedUser = User::find(request('assigned_to'));
                $assignedUserId = $assignedUser ? $assignedUser->id : null;
            }

            $task = Task::create([
                'title' => request('title'),
                'description' => request('description', ''),
                'status' => request('status', 'todo'),
                'priority' => request('priority', 'medium'),
                'due_date' => request('due_date'),
                'project_id' => request('project_id'),
                'category_id' => request('category_id'),
                'assigned_to' => $assignedUserId,
                'created_by' => $currentUser->id,
                'board_column' => request('status', 'todo'),
                'position' => Task::where('project_id', request('project_id'))
                                 ->where('board_column', request('status', 'todo'))
                                 ->max('position') + 1,
                'progress' => request('progress', 0),
                'user_create' => session('username')
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'create_task',
                'description' => 'Created new task: ' . $task->title,
                'model_type' => Task::class,
                'model_id' => $task->id,
                'user_create' => session('username')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'task' => $task->load(['assignee', 'category', 'creator'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update task
     */
    private function updateTask()
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail(request('task_id'));
            $oldValues = $task->toArray();

            $assignedUserId = null;
            if (request('assigned_to')) {
                $assignedUser = User::find(request('assigned_to'));
                $assignedUserId = $assignedUser ? $assignedUser->id : null;
            }

            $task->update([
                'title' => request('title', $task->title),
                'description' => request('description', $task->description),
                'priority' => request('priority', $task->priority),
                'due_date' => request('due_date', $task->due_date),
                'category_id' => request('category_id', $task->category_id),
                'assigned_to' => $assignedUserId,
                'progress' => request('progress', $task->progress),
                'user_update' => session('username')
            ]);

            // Log activity
            $currentUser = User::where('username', session('username'))->first();
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'update_task',
                'description' => 'Updated task: ' . $task->title,
                'model_type' => Task::class,
                'model_id' => $task->id,
                'user_create' => session('username')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $task->load(['assignee', 'category', 'creator'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Move task between columns
     */
    private function moveTask()
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail(request('task_id'));
            $oldColumn = $task->board_column;
            $newColumn = request('new_column');
            $newPosition = request('new_position', 0);

            // Update positions in old column
            Task::where('project_id', $task->project_id)
                ->where('board_column', $oldColumn)
                ->where('position', '>', $task->position)
                ->decrement('position');

            // Update positions in new column
            Task::where('project_id', $task->project_id)
                ->where('board_column', $newColumn)
                ->where('position', '>=', $newPosition)
                ->increment('position');

            // Update task
            $task->update([
                'board_column' => $newColumn,
                'status' => $newColumn,
                'position' => $newPosition,
                'user_update' => session('username')
            ]);

            // Log activity
            $currentUser = User::where('username', session('username'))->first();
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'move_task',
                'description' => "Moved task '{$task->title}' from {$oldColumn} to {$newColumn}",
                'model_type' => Task::class,
                'model_id' => $task->id,
                'user_create' => session('username')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task moved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get single task
     */
    private function getTask()
    {
        try {
            $task = Task::with(['assignee', 'category', 'creator'])
                       ->findOrFail(request('task_id'));

            return response()->json([
                'success' => true,
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                    'category_id' => $task->category_id,
                    'assigned_to' => $task->assigned_to,
                    'progress' => $task->progress,
                    'project_id' => $task->project_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Delete task
     */
    private function deleteTask()
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail(request('task_id'));
            $taskTitle = $task->title;

            // Log activity before deletion
            $currentUser = User::where('username', session('username'))->first();
            ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'delete_task',
                'description' => 'Deleted task: ' . $taskTitle,
                'model_type' => Task::class,
                'model_id' => $task->id,
                'user_create' => session('username')
            ]);

            // Update positions in column
            Task::where('project_id', $task->project_id)
                ->where('board_column', $task->board_column)
                ->where('position', '>', $task->position)
                ->decrement('position');

            $task->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
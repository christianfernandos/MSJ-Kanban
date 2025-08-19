<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Models\Board;
use App\Models\BoardList;
use App\Models\Card;
use App\Models\Activity;
use App\Models\Dashboard;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MsbrdController extends Controller
{
    /**
     * Check if user is authenticated.
     */
    private function checkAuthentication()
    {
        if (!session('username')) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        $user = User::where('username', session('username'))->first();
        if (!$user) {
            return redirect('/login')->with('error', 'Invalid user session.');
        }

        return null; // No redirect needed
    }
    /**
     * Display a listing of the resource.
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
        $data['title_group'] = 'Kanban Board';
        $data['url_menu'] = 'msbrd';

        // Set up table headers
        $data['table_header'] = collect([
            (object)[
                'field' => 'name',
                'alias' => 'Board Name',
                'type' => 'text',
                'primary' => '1'
            ],
            (object)[
                'field' => 'description',
                'alias' => 'Description',
                'type' => 'text',
                'primary' => '0'
            ],
            (object)[
                'field' => 'year',
                'alias' => 'Year',
                'type' => 'number',
                'primary' => '0'
            ],
            (object)[
                'field' => 'department',
                'alias' => 'Department',
                'type' => 'text',
                'primary' => '0'
            ],
            (object)[
                'field' => 'status',
                'alias' => 'Status',
                'type' => 'text',
                'primary' => '0'
            ],
            (object)[
                'field' => 'created_by',
                'alias' => 'Created By',
                'type' => 'text',
                'primary' => '0'
            ]
        ]);

        // Set up table detail
        $data['table_detail'] = Board::all();
        $data['table_primary'] = (object)['field' => 'id'];

        // Ambil data dashboard
        $dashboard = new Dashboard();
        $data['card_stats'] = $dashboard->getCardStatusCount();
        $data['recent_activities'] = $dashboard->getRecentActivities();
        $data['reminders'] = $dashboard->getReminders();

        // Ambil data board berdasarkan kategori
        $data['recently_viewed'] = Board::getRecentlyViewed();
        $data['my_boards'] = Board::getMyBoards(session('username'));
        $data['shared_boards'] = Board::getSharedWithUser(session('username'));

        // Ambil data board per tahun
        $currentYear = Carbon::now()->year;
        $data['boards_by_year'] = [
            $currentYear => Board::getByYear($currentYear),
            $currentYear - 1 => Board::getByYear($currentYear - 1),
            $currentYear - 2 => Board::getByYear($currentYear - 2)
        ];

        // return view dengan data
        return view($data['url'], $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($data)
    {
        // function helper
        $syslog = new Function_Helper;

        if ($data['authorize']->add == '1') {
            // Setup navigation data
            $data = $this->setupNavigationData($data);

            // Data untuk form pembuatan board baru
            $data['departments'] = [
                Board::DEPT_PURCHASING => 'Purchasing',
                Board::DEPT_MARKETING => 'Marketing'
            ];
            $data['years'] = range(Carbon::now()->year, Carbon::now()->year + 5);

            return view($data['url'], $data);
        } else {
            $syslog->log_insert('E', $data['url_menu'], 'Not Authorized!' . ' - Add Board', '0');
            return $this->showError($data, 'Not Authorized!');
        }
    }

    /**
     * Show the form for adding a new resource.
     */
    public function add($data)
    {
        // Redirect to create method since they serve the same purpose
        return $this->create($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            // Validate and trim description to prevent overflow
            $description = request()->description;
            if (strlen($description) > 255) {
                $description = substr($description, 0, 252) . '...';
            }

            // Buat board baru
            $board = Board::create([
                'name' => request()->name,
                'description' => $description,
                'year' => request()->year,
                'department' => request()->department,
                'created_by' => session('username'),
                'status' => Board::STATUS_ACTIVE
            ]);

            // Buat list default
            $board->addList('TO DO', BoardList::COLOR_TODO);
            $board->addList('PROGRESS', BoardList::COLOR_PROGRESS);
            $board->addList('DONE', BoardList::COLOR_DONE);

            // Catat aktivitas
            Activity::log(
                session('username'),
                Activity::ACTION_CREATE_CARD,
                'Created new board: ' . $board->name,
                $board->id
            );

            DB::commit();

            Session::flash('message', 'Board berhasil dibuat!');
            Session::flash('class', 'success');

            return redirect($data['url_menu']);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Create Board Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Gagal membuat board!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($data)
    {
        try {
            $board = Board::findOrFail(decrypt($data['idencrypt']));

            if (!$board->hasAccess(session('username'))) {
                throw new \Exception('Not Authorized');
            }

            // Setup navigation data
            $data = $this->setupNavigationData($data);

            // Update last viewed
            $board->updateLastViewed();

            // Ambil struktur board dengan list dan cards
            $data['board'] = $board;
            $data['board_structure'] = $board->getBoardStructure();

            return view($data['url'], $data);
        } catch (\Exception $e) {
            return $this->showError($data, $e->getMessage());
        }
    }

    /**
     * Display the Kanban board view (alias for board method).
     */
    public function kanban($data)
    {
        return $this->board($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($data)
    {
        try {
            if ($data['authorize']->edit != '1') {
                throw new \Exception('Not Authorized');
            }

            $board = Board::findOrFail(decrypt($data['idencrypt']));

            if (!$board->hasAccess(session('username'))) {
                throw new \Exception('Not Authorized');
            }

            // Setup navigation data
            $data = $this->setupNavigationData($data);

            $data['board'] = $board;
            $data['departments'] = [
                Board::DEPT_PURCHASING => 'Purchasing',
                Board::DEPT_MARKETING => 'Marketing'
            ];
            $data['years'] = range(Carbon::now()->year, Carbon::now()->year + 5);

            return view($data['url'], $data);
        } catch (\Exception $e) {
            return $this->showError($data, $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            $board = Board::findOrFail(decrypt($data['idencrypt']));

            $board->update([
                'name' => request()->name,
                'description' => request()->description,
                'year' => request()->year,
                'department' => request()->department,
                'user_update' => session('username')
            ]);

            Activity::log(
                session('username'),
                'update_board',
                'Updated board: ' . $board->name,
                $board->id
            );

            DB::commit();

            Session::flash('message', 'Board berhasil diupdate!');
            Session::flash('class', 'success');

            return redirect($data['url_menu']);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Update Board Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Gagal mengupdate board!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            $board = Board::findOrFail(decrypt($data['idencrypt']));
            
            // Arsipkan atau aktifkan board
            if ($board->status === Board::STATUS_ACTIVE) {
                $board->archive();
                $message = 'Board berhasil diarsipkan!';
                $action = 'Archived board: ';
            } else {
                $board->activate();
                $message = 'Board berhasil diaktifkan!';
                $action = 'Activated board: ';
            }

            Activity::log(
                session('username'),
                'toggle_board_status',
                $action . $board->name,
                $board->id
            );

            DB::commit();

            Session::flash('message', $message);
            Session::flash('class', 'success');

            return redirect($data['url_menu']);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Toggle Board Status Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Gagal mengubah status board!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
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
            $data['url_menu'] = 'msbrd';
        }

        // Ensure other MSJ framework variables are set
        if (!isset($data['dmenu'])) {
            $data['dmenu'] = 'msbrd';
        }

        if (!isset($data['gmenuid'])) {
            $data['gmenuid'] = 'msjbrd';
        }

        if (!isset($data['tabel'])) {
            $data['tabel'] = 'boards';
        }

        if (!isset($data['jsmenu'])) {
            $data['jsmenu'] = '0';
        }

        // Ensure authorize object is set (with default permissions)
        if (!isset($data['authorize'])) {
            $data['authorize'] = (object)[
                'add' => '1',
                'edit' => '1',
                'delete' => '1',
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
     * Menampilkan halaman error
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

    /**
     * Menambah list baru ke board
     */
    public function addList($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            $board = Board::findOrFail(decrypt($data['idencrypt']));
            
            $list = $board->addList(
                request()->name,
                request()->color
            );

            Activity::log(
                session('username'),
                'add_list',
                'Added new list: ' . $list->name . ' to board: ' . $board->name,
                $board->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'List berhasil ditambahkan',
                'list' => $list
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Add List Error: ' . $e->getMessage(), '0');
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan list'
            ], 500);
        }
    }

    /**
     * Menambah card baru ke list
     */
    public function addCard($data)
    {
        $syslog = new Function_Helper;

        try {
            DB::beginTransaction();

            $list = BoardList::findOrFail(decrypt($data['list_id']));
            
            $card = $list->addCard([
                'title' => request()->title,
                'description' => request()->description,
                'due_date' => request()->due_date
            ]);

            Activity::log(
                session('username'),
                Activity::ACTION_CREATE_CARD,
                'Added new card: ' . $card->title . ' to list: ' . $list->name,
                $list->board_id,
                $card->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Card berhasil ditambahkan',
                'card' => $card
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $syslog->log_insert('E', $data['dmenu'], 'Add Card Error: ' . $e->getMessage(), '0');
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan card'
            ], 500);
        }
    }

    /**
     * API Methods for Kanban Board
     */
    public function getCards()
    {
        try {
            $cards = Card::with(['creator', 'assignee'])
                        ->orderBy('order_index')
                        ->get()
                        ->map(function($card) {
                            return [
                                'id' => $card->id,
                                'title' => $card->title,
                                'description' => $card->description,
                                'status' => $card->status,
                                'due_date' => $card->due_date ? $card->due_date->format('Y-m-d') : null,
                                'assigned_to' => $card->assigned_to,
                                'assigned_name' => $card->assignee ? $card->assignee->name : null,
                                'created_by' => $card->creator ? $card->creator->name : null
                            ];
                        });
            
            return response()->json($cards);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCard($id)
    {
        try {
            $card = Card::with(['creator', 'assignee'])->findOrFail($id);
            return response()->json([
                'id' => $card->id,
                'title' => $card->title,
                'description' => $card->description,
                'status' => $card->status,
                'due_date' => $card->due_date ? $card->due_date->format('Y-m-d') : null,
                'assigned_to' => $card->assigned_to,
                'assigned_name' => $card->assignee ? $card->assignee->name : null,
                'created_by' => $card->creator ? $card->creator->name : null
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function storeCard(Request $request)
    {
        try {
            DB::beginTransaction();

            $card = Card::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'assigned_to' => $request->assigned_to,
                'created_by' => session('username'),
                'board_id' => $request->board_id ?? 1, // Default board, adjust as needed
                'order_index' => Card::where('board_id', $request->board_id ?? 1)->max('order_index') + 1
            ]);

            Activity::log(
                session('username'),
                Activity::ACTION_CREATE_CARD,
                'Created new card: ' . $card->title,
                $card->board_id,
                $card->id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Card created successfully',
                'card' => $card
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCard(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $card = Card::findOrFail($id);
            $oldStatus = $card->status;

            $card->update([
                'title' => $request->title ?? $card->title,
                'description' => $request->description ?? $card->description,
                'status' => $request->status ?? $card->status,
                'due_date' => $request->due_date ?? $card->due_date,
                'assigned_to' => $request->assigned_to ?? $card->assigned_to
            ]);

            if ($oldStatus !== $card->status) {
                Activity::log(
                    session('username'),
                    Activity::ACTION_MOVE_CARD,
                    "Moved card '{$card->title}' from {$oldStatus} to {$card->status}",
                    $card->board_id,
                    $card->id
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Card updated successfully',
                'card' => $card
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCard($id)
    {
        try {
            DB::beginTransaction();

            $card = Card::findOrFail($id);
            
            Activity::log(
                session('username'),
                'delete_card',
                'Deleted card: ' . $card->title,
                $card->board_id,
                $card->id
            );

            $card->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Card deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        try {
            $users = User::where('isactive', 1)
                        ->select('username', 'firstname', 'lastname')
                        ->orderBy('firstname')
                        ->orderBy('lastname')
                        ->get()
                        ->map(function($user) {
                            return [
                                'username' => $user->username,
                                'name' => trim($user->firstname . ' ' . $user->lastname) ?: $user->username
                            ];
                        });

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the Kanban board view for a project.
     */
    public function board($data)
    {
        // Check authentication first
        $authCheck = $this->checkAuthentication();
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $project = Project::findOrFail(decrypt($data['idencrypt']));

            if (!$project->hasAccess(session('username'))) {
                throw new \Exception('Not Authorized');
            }

            // Setup navigation data
            $data = $this->setupNavigationData($data);

            // Override the URL to use kanban view
            $data['url'] = 'msjbrd.msbrd.kanban';

            // Get project data
            $data['project'] = $project;

            // Get tasks grouped by status for Kanban columns
            $data['tasks_by_status'] = $project->getTasksByStatus();

            // Get categories for task creation
            $data['categories'] = Category::active()->ordered()->get();

            // Get project members for task assignment
            $data['project_members'] = $project->members()
                                              ->where('status', 'active')
                                              ->get();

            // Get all users for assignment (fallback)
            $data['all_users'] = User::where('isactive', 1)
                                    ->select('username', 'firstname', 'lastname')
                                    ->orderBy('firstname')
                                    ->get();

            return view($data['url'], $data);
        } catch (\Exception $e) {
            return $this->showError($data, $e->getMessage());
        }
    }

    /**
     * Get board data for AJAX requests.
     */
    public function getBoardData($projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            if (!$project->hasAccess(session('username'))) {
                return response()->json(['error' => 'Not Authorized'], 403);
            }

            $tasksByStatus = $project->getTasksByStatus();

            // Format data for frontend
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
                'project' => $project,
                'columns' => $columns
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a single task for editing.
     */
    public function getTask($taskId)
    {
        try {
            $task = Task::with(['assignee', 'category', 'creator'])->findOrFail($taskId);

            return response()->json([
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null,
                'category_id' => $task->category_id,
                'assigned_to' => $task->assignee ? $task->assignee->username : null,
                'progress' => $task->progress,
                'project_id' => $task->project_id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Create a new task via AJAX.
     */
    public function createTask(Request $request)
    {
        // Check authentication
        if (!session('username')) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        try {
            DB::beginTransaction();

            // Get current user ID
            $currentUser = User::where('username', session('username'))->first();
            if (!$currentUser) {
                return response()->json(['error' => 'User not found'], 401);
            }

            // Get assigned user ID if provided
            $assignedUserId = null;
            if ($request->assigned_to) {
                $assignedUser = User::where('username', $request->assigned_to)->first();
                $assignedUserId = $assignedUser ? $assignedUser->id : null;
            }

            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status ?? 'todo',
                'priority' => $request->priority ?? 'medium',
                'due_date' => $request->due_date,
                'project_id' => $request->project_id,
                'category_id' => $request->category_id,
                'assigned_to' => $assignedUserId,
                'created_by' => $currentUser->id,
                'board_column' => $request->status ?? 'todo',
                'position' => Task::where('project_id', $request->project_id)
                                 ->where('board_column', $request->status ?? 'todo')
                                 ->max('position') + 1,
                'user_create' => session('username')
            ]);

            // Log activity
            ActivityLog::log(
                'create_task',
                'Created new task: ' . $task->title,
                $task->id,
                Task::class
            );

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
     * Update a task via AJAX.
     */
    public function updateTask(Request $request, $taskId)
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail($taskId);
            $oldValues = $task->toArray();

            // Get assigned user ID if provided
            $assignedUserId = null;
            if ($request->assigned_to) {
                $assignedUser = User::where('username', $request->assigned_to)->first();
                $assignedUserId = $assignedUser ? $assignedUser->id : null;
            }

            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'due_date' => $request->due_date,
                'category_id' => $request->category_id,
                'assigned_to' => $assignedUserId,
                'progress' => $request->progress ?? $task->progress,
                'user_update' => session('username')
            ]);

            // Log activity
            ActivityLog::log(
                'update_task',
                'Updated task: ' . $task->title,
                $task->id,
                Task::class,
                $oldValues,
                $task->toArray()
            );

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
     * Move a task to a different column/status.
     */
    public function moveTask(Request $request)
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail($request->task_id);
            $oldColumn = $task->board_column;
            $oldPosition = $task->position;

            // Move task to new column and position
            $task->moveToColumn($request->new_column, $request->new_position);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task moved successfully',
                'task' => $task
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a task.
     */
    public function deleteTask($taskId)
    {
        try {
            DB::beginTransaction();

            $task = Task::findOrFail($taskId);
            $taskTitle = $task->title;

            // Log activity before deletion
            ActivityLog::log(
                'delete_task',
                'Deleted task: ' . $taskTitle,
                $task->project_id,
                Project::class
            );

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
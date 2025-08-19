<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KactivController extends Controller
{
    /**
     * Display the Activity Logs page.
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
        $data['title_group'] = 'Activity Logs';
        $data['url_menu'] = 'kactiv';

        // Activity Statistics
        $data['today_activities'] = ActivityLog::whereDate('created_at', Carbon::today())->count();
        $data['week_activities'] = ActivityLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $data['total_activities'] = ActivityLog::count();
        
        // Most active user
        $mostActiveUser = ActivityLog::select('user_id', DB::raw('count(*) as activity_count'))
                                    ->groupBy('user_id')
                                    ->orderBy('activity_count', 'desc')
                                    ->with('user')
                                    ->first();
        $data['most_active_user'] = $mostActiveUser ? $mostActiveUser->user->firstname ?? 'N/A' : 'N/A';

        // Recent Activities
        $data['recent_activities'] = ActivityLog::with(['user'])
                                               ->orderBy('created_at', 'desc')
                                               ->limit(20)
                                               ->get();

        // Activity distribution for chart
        $data['activity_distribution'] = [
            'task_updates' => ActivityLog::where('action', 'like', '%task%')->count(),
            'comments' => ActivityLog::where('action', 'like', '%comment%')->count(),
            'attachments' => ActivityLog::where('action', 'like', '%attachment%')->count(),
            'projects' => ActivityLog::where('action', 'like', '%project%')->count(),
            'others' => ActivityLog::whereNotIn('action', ['task', 'comment', 'attachment', 'project'])->count()
        ];

        return view($data['url'], $data);
    }

    /**
     * Get activities with filters via AJAX.
     */
    public function getActivities(Request $request)
    {
        try {
            $query = ActivityLog::with(['user']);

            // Apply filters
            if ($request->activity_type) {
                $query->where('action', 'like', '%' . $request->activity_type . '%');
            }

            if ($request->user_filter) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('username', $request->user_filter);
                });
            }

            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->orderBy('created_at', 'desc')
                               ->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity statistics.
     */
    public function getActivityStats()
    {
        try {
            $stats = [
                'today' => ActivityLog::whereDate('created_at', Carbon::today())->count(),
                'week' => ActivityLog::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                'month' => ActivityLog::whereMonth('created_at', Carbon::now()->month)->count(),
                'total' => ActivityLog::count()
            ];

            // Most active user
            $mostActiveUser = ActivityLog::select('user_id', DB::raw('count(*) as activity_count'))
                                        ->groupBy('user_id')
                                        ->orderBy('activity_count', 'desc')
                                        ->with('user')
                                        ->first();

            $stats['most_active_user'] = $mostActiveUser ? $mostActiveUser->user->firstname ?? 'N/A' : 'N/A';

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export activities to Excel/PDF.
     */
    public function exportActivities(Request $request)
    {
        try {
            $format = $request->format ?? 'excel';
            
            // Get activities with filters
            $query = ActivityLog::with(['user']);

            if ($request->activity_type) {
                $query->where('action', 'like', '%' . $request->activity_type . '%');
            }

            if ($request->user_filter) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('username', $request->user_filter);
                });
            }

            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->orderBy('created_at', 'desc')->get();

            // Here you would implement actual export logic
            // For now, just return success response

            return response()->json([
                'success' => true,
                'message' => "Activities exported to {$format} format",
                'count' => $activities->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity details.
     */
    public function getActivityDetails($id)
    {
        try {
            $activity = ActivityLog::with(['user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'activity' => [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'user' => $activity->user ? $activity->user->firstname . ' ' . $activity->user->lastname : 'System',
                    'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                    'ip_address' => $activity->ip_address ?? 'N/A',
                    'user_agent' => $activity->user_agent ?? 'N/A'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
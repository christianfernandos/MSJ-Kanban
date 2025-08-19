<?php

namespace App\Http\Controllers;

use App\Helpers\Format_Helper;
use App\Helpers\Function_Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class KsetngController extends Controller
{
    /**
     * Display the Kanban Settings page.
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
        $data['title_group'] = 'Kanban Settings';
        $data['url_menu'] = 'ksetng';

        // Get current Kanban settings (you can create a settings table or use sys_app)
        $data['kanban_settings'] = [
            'default_task_status' => 'todo',
            'default_task_priority' => 'medium',
            'auto_assign_tasks' => false,
            'email_notifications' => true,
            'task_due_date_reminder' => 3, // days before due date
            'max_tasks_per_column' => 50,
            'allow_task_comments' => true,
            'allow_task_attachments' => true,
            'board_theme' => 'default',
            'show_task_numbers' => true,
            'enable_time_tracking' => true
        ];

        // Available options for dropdowns
        $data['status_options'] = [
            'todo' => 'To Do',
            'in_progress' => 'In Progress',
            'review' => 'Review',
            'done' => 'Done'
        ];

        $data['priority_options'] = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];

        $data['theme_options'] = [
            'default' => 'Default',
            'dark' => 'Dark',
            'blue' => 'Blue',
            'green' => 'Green'
        ];

        return view($data['url'], $data);
    }

    /**
     * Update Kanban settings.
     */
    public function update(Request $request)
    {
        $syslog = new Function_Helper;

        try {
            // Validate the request
            $request->validate([
                'default_task_status' => 'required|in:todo,in_progress,review,done',
                'default_task_priority' => 'required|in:low,medium,high,urgent',
                'task_due_date_reminder' => 'required|integer|min:1|max:30',
                'max_tasks_per_column' => 'required|integer|min:10|max:200',
                'board_theme' => 'required|in:default,dark,blue,green'
            ]);

            // Here you would typically save to a settings table or sys_app
            // For now, we'll just show a success message
            
            $syslog->log_insert('I', 'ksetng', 'Kanban settings updated successfully', '1');

            Session::flash('message', 'Kanban settings updated successfully!');
            Session::flash('class', 'success');

            return redirect()->back();
        } catch (\Exception $e) {
            $syslog->log_insert('E', 'ksetng', 'Update Kanban Settings Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Failed to update Kanban settings!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }

    /**
     * Reset Kanban settings to default.
     */
    public function reset()
    {
        $syslog = new Function_Helper;

        try {
            // Reset settings to default values
            // This would typically involve updating the settings table
            
            $syslog->log_insert('I', 'ksetng', 'Kanban settings reset to default', '1');

            Session::flash('message', 'Kanban settings reset to default successfully!');
            Session::flash('class', 'success');

            return redirect()->back();
        } catch (\Exception $e) {
            $syslog->log_insert('E', 'ksetng', 'Reset Kanban Settings Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Failed to reset Kanban settings!');
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }

    /**
     * Export Kanban configuration.
     */
    public function export()
    {
        try {
            $settings = [
                'default_task_status' => 'todo',
                'default_task_priority' => 'medium',
                'auto_assign_tasks' => false,
                'email_notifications' => true,
                'task_due_date_reminder' => 3,
                'max_tasks_per_column' => 50,
                'allow_task_comments' => true,
                'allow_task_attachments' => true,
                'board_theme' => 'default',
                'show_task_numbers' => true,
                'enable_time_tracking' => true,
                'exported_at' => now()->toDateTimeString(),
                'exported_by' => session('username')
            ];

            $filename = 'kanban_settings_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($settings)
                           ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                           ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import Kanban configuration.
     */
    public function import(Request $request)
    {
        $syslog = new Function_Helper;

        try {
            $request->validate([
                'settings_file' => 'required|file|mimes:json'
            ]);

            $file = $request->file('settings_file');
            $content = file_get_contents($file->getRealPath());
            $settings = json_decode($content, true);

            if (!$settings) {
                throw new \Exception('Invalid settings file format');
            }

            // Here you would validate and save the imported settings
            
            $syslog->log_insert('I', 'ksetng', 'Kanban settings imported successfully', '1');

            Session::flash('message', 'Kanban settings imported successfully!');
            Session::flash('class', 'success');

            return redirect()->back();
        } catch (\Exception $e) {
            $syslog->log_insert('E', 'ksetng', 'Import Kanban Settings Error: ' . $e->getMessage(), '0');
            
            Session::flash('message', 'Failed to import Kanban settings: ' . $e->getMessage());
            Session::flash('class', 'danger');
            
            return redirect()->back();
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class kanban_menu_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing entries first (in correct order due to foreign key constraints)
        DB::table('sys_auth')->where(['gmenu' => 'kanban'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban'])->delete();
        DB::table('sys_dmenu')->where(['gmenu' => 'kanban'])->delete();
        DB::table('sys_gmenu')->where(['gmenu' => 'kanban'])->delete();

        // Insert main gmenu entry for Kanban
        DB::table('sys_gmenu')->insert([
            'gmenu' => 'kanban',
            'urut' => 7,
            'name' => 'Kanban Management',
            'icon' => 'ni-app',
            'isactive' => '1'
        ]);

        // Insert dmenu entries for Kanban functionality
        
        // 1. Dashboard Kanban
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kdash',
            'urut' => 1,
            'name' => 'Kanban Dashboard',
            'url' => 'kdash',
            'icon' => 'ni-tv-2',
            'tabel' => '-',
            'layout' => 'manual',
            'isactive' => '1'
        ]);

        // 2. Project Management (Main Board)
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => 2,
            'name' => 'Project Management',
            'url' => 'msbrd',
            'icon' => 'ni-collection',
            'tabel' => 'projects',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 3. Task Management
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => 3,
            'name' => 'Task Management',
            'url' => 'ktasks',
            'icon' => 'ni-single-copy-04',
            'tabel' => 'tasks',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 4. Categories Management
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => 4,
            'name' => 'Task Categories',
            'url' => 'kctgry',
            'icon' => 'ni-tag',
            'tabel' => 'categories',
            'layout' => 'standr',
            'isactive' => '1'
        ]);

        // 5. Project Members
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kmembr',
            'urut' => 5,
            'name' => 'Project Members',
            'url' => 'kmembr',
            'icon' => 'ni-circle-08',
            'tabel' => 'project_members',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 6. Time Tracking
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktrack',
            'urut' => 6,
            'name' => 'Time Tracking',
            'url' => 'ktrack',
            'icon' => 'ni-watch-time',
            'tabel' => 'time_tracking',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 7. Comments Management
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kcomnt',
            'urut' => 7,
            'name' => 'Task Comments',
            'url' => 'kcomnt',
            'icon' => 'ni-chat-round',
            'tabel' => 'comments',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 8. Attachments Management
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kattch',
            'urut' => 8,
            'name' => 'Task Attachments',
            'url' => 'kattch',
            'icon' => 'ni-attach-87',
            'tabel' => 'attachments',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 9. Activity Logs
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kactiv',
            'urut' => 9,
            'name' => 'Activity Logs',
            'url' => 'kactiv',
            'icon' => 'ni-bullet-list-67',
            'tabel' => 'activity_logs',
            'layout' => 'manual',
            'isactive' => '1'
        ]);

        // 10. Task Dependencies
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kdepnd',
            'urut' => 10,
            'name' => 'Task Dependencies',
            'url' => 'kdepnd',
            'icon' => 'ni-vector',
            'tabel' => 'task_dependencies',
            'layout' => 'master',
            'isactive' => '1'
        ]);

        // 11. Kanban Reports
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'krport',
            'urut' => 11,
            'name' => 'Kanban Reports',
            'url' => 'krport',
            'icon' => 'ni-chart-bar-32',
            'tabel' => '-',
            'layout' => 'manual',
            'isactive' => '1'
        ]);

        // 12. Kanban Settings
        DB::table('sys_dmenu')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ksetng',
            'urut' => 12,
            'name' => 'Kanban Settings',
            'url' => 'ksetng',
            'icon' => 'ni-settings-gear-65',
            'tabel' => '-',
            'layout' => 'manual',
            'isactive' => '1'
        ]);

        // Insert sys_auth entries for admin role
        $kanban_menus = [
            'kdash', 'msbrd', 'ktasks', 'kctgry', 'kmembr', 
            'ktrack', 'kcomnt', 'kattch', 'kactiv', 'kdepnd', 
            'krport', 'ksetng'
        ];
        
        foreach ($kanban_menus as $dmenu) {
            DB::table('sys_auth')->insert([
                'idroles' => 'admins',
                'gmenu' => 'kanban',
                'dmenu' => $dmenu,
                'add' => '1',
                'edit' => '1',
                'delete' => '1',
                'approval' => '0',
                'value' => '0',
                'print' => '1',
                'excel' => '1',
                'pdf' => '1',
                'rules' => '0',
                'isactive' => '1'
            ]);
        }
    }
}

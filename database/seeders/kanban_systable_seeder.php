<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class kanban_systable_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing sys_table entries for kanban
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'msbrd'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'ktasks'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'kctgry'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'kmembr'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'ktrack'])->delete();
        DB::table('sys_table')->where(['gmenu' => 'kanban', 'dmenu' => 'kactiv'])->delete();

        // ===== PROJECT BOARD (msbrd) TABLE CONFIGURATION =====
        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '1',
            'field' => 'id',
            'alias' => 'ID',
            'type' => 'number',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '2',
            'field' => 'name',
            'alias' => 'Project Name',
            'type' => 'string',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '3',
            'field' => 'description',
            'alias' => 'Description',
            'type' => 'text',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '0',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '4',
            'field' => 'status',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '20',
            'decimals' => '0',
            'default' => 'active',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select 'active' as value, 'Active' as name union select 'inactive' as value, 'Inactive' as name union select 'completed' as value, 'Completed' as name union select 'on_hold' as value, 'On Hold' as name"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '5',
            'field' => 'priority',
            'alias' => 'Priority',
            'type' => 'enum',
            'length' => '10',
            'decimals' => '0',
            'default' => 'medium',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select 'low' as value, 'Low' as name union select 'medium' as value, 'Medium' as name union select 'high' as value, 'High' as name union select 'urgent' as value, 'Urgent' as name"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '6',
            'field' => 'start_date',
            'alias' => 'Start Date',
            'type' => 'date',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|date',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '7',
            'field' => 'end_date',
            'alias' => 'End Date',
            'type' => 'date',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|date|after_or_equal:start_date',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'msbrd',
            'urut' => '8',
            'field' => 'created_by',
            'alias' => 'Created By',
            'type' => 'enum',
            'length' => '50',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select username as value, concat(firstname, ' ', lastname) as name from users where isactive = '1'"
        ]);

        // ===== CATEGORIES (kctgry) TABLE CONFIGURATION =====
        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '1',
            'field' => 'id',
            'alias' => 'ID',
            'type' => 'number',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '2',
            'field' => 'name',
            'alias' => 'Category Name',
            'type' => 'string',
            'length' => '100',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:100|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '3',
            'field' => 'color',
            'alias' => 'Color',
            'type' => 'string',
            'length' => '7',
            'decimals' => '0',
            'default' => '#007bff',
            'validate' => 'required|max:7',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '4',
            'field' => 'icon',
            'alias' => 'Icon',
            'type' => 'string',
            'length' => '50',
            'decimals' => '0',
            'default' => 'ni-tag',
            'validate' => 'nullable|max:50',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '5',
            'field' => 'description',
            'alias' => 'Description',
            'type' => 'text',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '0',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '6',
            'field' => 'sort_order',
            'alias' => 'Sort Order',
            'type' => 'number',
            'length' => '11',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'nullable|numeric',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'kctgry',
            'urut' => '7',
            'field' => 'isactive',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '1',
            'decimals' => '0',
            'default' => '1',
            'validate' => '',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select value, name from sys_enum where idenum = 'isactive' and isactive = '1'"
        ]);

        // ===== TASKS (ktasks) TABLE CONFIGURATION =====
        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '1',
            'field' => 'id',
            'alias' => 'ID',
            'type' => 'number',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => '',
            'primary' => '1',
            'filter' => '1',
            'list' => '1',
            'show' => '0',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '2',
            'field' => 'title',
            'alias' => 'Task Title',
            'type' => 'string',
            'length' => '255',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|max:255|min:1',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '3',
            'field' => 'description',
            'alias' => 'Description',
            'type' => 'text',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable',
            'primary' => '0',
            'filter' => '0',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '4',
            'field' => 'status',
            'alias' => 'Status',
            'type' => 'enum',
            'length' => '20',
            'decimals' => '0',
            'default' => 'todo',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select 'todo' as value, 'To Do' as name union select 'in_progress' as value, 'In Progress' as name union select 'review' as value, 'Review' as name union select 'done' as value, 'Done' as name union select 'cancelled' as value, 'Cancelled' as name"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '5',
            'field' => 'priority',
            'alias' => 'Priority',
            'type' => 'enum',
            'length' => '10',
            'decimals' => '0',
            'default' => 'medium',
            'validate' => 'required',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select 'low' as value, 'Low' as name union select 'medium' as value, 'Medium' as name union select 'high' as value, 'High' as name union select 'urgent' as value, 'Urgent' as name"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '6',
            'field' => 'project_id',
            'alias' => 'Project',
            'type' => 'enum',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => 'required|exists:projects,id',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select id as value, name from projects where isactive = '1'"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '7',
            'field' => 'category_id',
            'alias' => 'Category',
            'type' => 'enum',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|exists:categories,id',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select id as value, name from categories where isactive = '1'"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '8',
            'field' => 'assigned_to',
            'alias' => 'Assigned To',
            'type' => 'enum',
            'length' => '11',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|exists:users,id',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => "select id as value, concat(firstname, ' ', lastname) as name from users where isactive = '1'"
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '9',
            'field' => 'due_date',
            'alias' => 'Due Date',
            'type' => 'date',
            'length' => '0',
            'decimals' => '0',
            'default' => '',
            'validate' => 'nullable|date',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '10',
            'field' => 'estimated_hours',
            'alias' => 'Estimated Hours',
            'type' => 'decimal',
            'length' => '8',
            'decimals' => '2',
            'default' => '',
            'validate' => 'nullable|numeric|min:0',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);

        DB::table('sys_table')->insert([
            'gmenu' => 'kanban',
            'dmenu' => 'ktasks',
            'urut' => '11',
            'field' => 'progress',
            'alias' => 'Progress (%)',
            'type' => 'number',
            'length' => '3',
            'decimals' => '0',
            'default' => '0',
            'validate' => 'nullable|numeric|min:0|max:100',
            'primary' => '0',
            'filter' => '1',
            'list' => '1',
            'show' => '1',
            'query' => ''
        ]);
    }
}

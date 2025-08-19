<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class KanbanSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories only if they don't exist
        if (Category::count() == 0) {
            $categories = [
            [
                'name' => 'Bug Fix',
                'description' => 'Bug fixes and error corrections',
                'color' => '#dc3545',
                'icon' => 'fas fa-bug',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Feature',
                'description' => 'New features and enhancements',
                'color' => '#28a745',
                'icon' => 'fas fa-plus',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Documentation',
                'description' => 'Documentation updates',
                'color' => '#17a2b8',
                'icon' => 'fas fa-book',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Testing',
                'description' => 'Testing and QA tasks',
                'color' => '#ffc107',
                'icon' => 'fas fa-vial',
                'is_active' => true,
                'sort_order' => 4
            ]
        ];

            foreach ($categories as $categoryData) {
                Category::create($categoryData);
            }
        }

        // Create sample projects
        $projects = [
            [
                'name' => 'E-Commerce Platform',
                'description' => 'Building a modern e-commerce platform with advanced features',
                'status' => 'active',
                'priority' => 'high',
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->addDays(60),
                'budget' => 50000.00,
                'color' => '#667eea',
                'owner_id' => 1, // User ID 1 (msjit)
                'user_create' => 'msjit'
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Cross-platform mobile application development',
                'status' => 'active',
                'priority' => 'medium',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(45),
                'budget' => 30000.00,
                'color' => '#f093fb',
                'owner_id' => 1,
                'user_create' => 'msjit'
            ]
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Create sample tasks for each project
            $this->createSampleTasks($project);
        }
    }

    private function createSampleTasks(Project $project)
    {
        $categories = Category::all();
        $users = [1]; // User IDs

        $sampleTasks = [
            // TO DO tasks
            [
                'title' => 'Set up project structure',
                'description' => 'Initialize the project with proper folder structure and dependencies',
                'status' => 'todo',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(3),
                'board_column' => 'todo',
                'position' => 1,
                'progress' => 0
            ],
            [
                'title' => 'Design database schema',
                'description' => 'Create comprehensive database design with all necessary tables and relationships',
                'status' => 'todo',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(5),
                'board_column' => 'todo',
                'position' => 2,
                'progress' => 0
            ],
            [
                'title' => 'Create user authentication system',
                'description' => 'Implement secure user registration, login, and password reset functionality',
                'status' => 'todo',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(7),
                'board_column' => 'todo',
                'position' => 3,
                'progress' => 0
            ],

            // IN PROGRESS tasks
            [
                'title' => 'Implement product catalog',
                'description' => 'Build the product listing, search, and filtering functionality',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(10),
                'board_column' => 'in_progress',
                'position' => 1,
                'progress' => 45
            ],
            [
                'title' => 'Shopping cart functionality',
                'description' => 'Develop add to cart, update quantities, and checkout process',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(8),
                'board_column' => 'in_progress',
                'position' => 2,
                'progress' => 30
            ],

            // REVIEW tasks
            [
                'title' => 'Payment gateway integration',
                'description' => 'Integrate multiple payment methods including credit cards and PayPal',
                'status' => 'review',
                'priority' => 'urgent',
                'due_date' => Carbon::now()->addDays(2),
                'board_column' => 'review',
                'position' => 1,
                'progress' => 90
            ],
            [
                'title' => 'Order management system',
                'description' => 'Build admin panel for managing orders, inventory, and customers',
                'status' => 'review',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(4),
                'board_column' => 'review',
                'position' => 2,
                'progress' => 85
            ],

            // DONE tasks
            [
                'title' => 'Project planning and requirements',
                'description' => 'Complete project scope definition and technical requirements gathering',
                'status' => 'done',
                'priority' => 'high',
                'due_date' => Carbon::now()->subDays(5),
                'completed_at' => Carbon::now()->subDays(3),
                'board_column' => 'done',
                'position' => 1,
                'progress' => 100
            ],
            [
                'title' => 'UI/UX design mockups',
                'description' => 'Create wireframes and high-fidelity designs for all major pages',
                'status' => 'done',
                'priority' => 'medium',
                'due_date' => Carbon::now()->subDays(10),
                'completed_at' => Carbon::now()->subDays(7),
                'board_column' => 'done',
                'position' => 2,
                'progress' => 100
            ]
        ];

        foreach ($sampleTasks as $index => $taskData) {
            $taskData['project_id'] = $project->id;
            $taskData['created_by'] = $users[0];
            $taskData['assigned_to'] = $users[array_rand($users)];
            $taskData['category_id'] = $categories->random()->id;
            $taskData['user_create'] = 'msjit';

            Task::create($taskData);
        }
    }
}


@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Kanban Board'])
    
    <style>
        /* Kanban specific styles - adjusted for MSJ Framework */
        .kanban-container {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            min-height: calc(100vh - 200px);
            padding: 0;
            margin: 0;
        }

        /* Page Navigation Container - separated and positioned higher */
        .page-nav-container {
            position: relative;
            z-index: 100; /* Lowered z-index to avoid conflicts with modal */
            margin-top: -60px;
            margin-bottom: 20px;
        }

        .page-nav {
            color: #344767;
            padding: 0 20px;
        }

        .page-nav-light {
            font-size: 18px;
            font-weight: 300;
            margin-bottom: 5px;
        }

        .page-nav-bold {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Back Button Container - separated from search */
        .back-btn-container {
            position: relative;
            z-index: 100; /* Lowered z-index to avoid conflicts with modal */
            margin: 0 20px 25px 20px;
        }

        .back-btn {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 8px 16px;
            color: #344767;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .back-btn:hover {
            background: #f8f9fa;
        }

        /* Search Bar Container - separated from back button */
        .search-section {
            margin: 0 20px 20px 20px;
            position: relative;
            z-index: 100; /* Lowered z-index to avoid conflicts with modal */
        }

        .search-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            height: 50px;
            width: calc(100vw - 300px);
            max-width: none;
            display: flex;
            align-items: center;
            padding: 0 16px;
        }

        .search-input {
            background: rgba(2,130,132,0.15);
            border-radius: 8px;
            width: 100%;
            height: 35px;
            display: flex;
            align-items: center;
            padding: 0 12px;
            border: none;
            outline: none;
            cursor: pointer;
        }

        .search-text {
            color: rgba(52,71,103,0.6);
            font-size: 14px;
            margin-left: 8px;
        }

        /* Content Container */
        .content-container {
            position: relative;
            margin: 20px;
        }

        .content-bg {
            border-radius: 20px;
            box-shadow: 0px 109px 337px 0px rgba(0,0,0,0.082), 0px 45.538px 140.791px 0px rgba(0,0,0,0.118), 0px 24.347px 75.273px 0px rgba(0,0,0,0.145), 0px 13.648px 42.198px 0px rgba(0,0,0,0.173), 0px 7.249px 22.411px 0px rgba(0,0,0,0.208), 0px 3.016px 9.326px 0px rgba(0,0,0,0.29);
            width: calc(100vw - 340px);
            max-width: none;
            height: calc(100vh - 120px);
            min-height: calc(100vh - 120px);
            position: relative;
            background-image: url('cover_in.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Header - will overlay on top of the background image */
        .header {
            position: absolute;
            top: 32px;
            left: 32px;
            right: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .header-title {
            color: #344767;
            font-size: 24px;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(255,255,255,0.8);
        }

        .header-actions {
            display: flex;
            gap: 16px;
        }

        .header-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.9);
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-btn:hover {
            background: rgba(255,255,255,1);
        }

        /* Kanban Columns */
        .kanban-columns {
            position: absolute;
            top: 100px;
            left: 32px;
            right: 32px;
            display: flex;
            gap: 32px;
            overflow-x: auto;
            padding-bottom: 32px;
        }

        .kanban-column {
            flex: 0 0 auto;
        }

        .column-container {
            background: white;
            background-image: url('cover_in.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 20px;
            box-shadow: 0px 109px 337px 0px rgba(0,0,0,0.082), 0px 45.538px 140.791px 0px rgba(0,0,0,0.118), 0px 24.347px 75.273px 0px rgba(0,0,0,0.145), 0px 13.648px 42.198px 0px rgba(0,0,0,0.173), 0px 7.249px 22.411px 0px rgba(0,0,0,0.208), 0px 3.016px 9.326px 0px rgba(0,0,0,0.29);
            width: 384px;
            min-height: 593px;
            padding: 24px;
            position: relative;
        }

        /* Add overlay to ensure text readability over the background image */
        .column-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            z-index: 1;
        }

        /* Ensure content appears above the overlay */
        .column-header,
        .cards-container {
            position: relative;
            z-index: 2;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .column-title {
            color: #344767;
            font-size: 20px;
            font-weight: 500;
        }

        .column-menu {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        /* Cards */
        .cards-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 400px;
        }

        .card {
            background: #f4f4f4;
            border-radius: 10px;
            padding: 16px;
            cursor: move;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card-label {
            width: 100%;
            height: 8px;
            border-radius: 2px;
            margin-bottom: 12px;
        }

        .card-label.red { background: #ff0000; }
        .card-label.blue { background: #1500ff; }
        .card-label.green { background: #00ff00; }
        .card-label.yellow { background: #ffff00; }

        .card-department {
            color: #6a6a6a;
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .card-description {
            color: #000000;
            font-size: 16px;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .card-date {
            color: #6a6a6a;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-stat {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-stat-icon {
            width: 20px;
            height: 20px;
        }

        .card-stat-text {
            color: #6a6a6a;
            font-size: 14px;
        }

        /* Add Card Button */
        .add-card {
            background: #f4f4f4;
            border-radius: 10px;
            padding: 16px;
            border: 2px dashed rgba(0,0,0,0.2);
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .add-card:hover {
            background: #e8e8e8;
        }

        .add-card-text {
            color: #6a6a6a;
            font-size: 16px;
        }

        /* Add List */
        .add-list {
            background: #f4f4f4;
            border-radius: 20px;
            width: 384px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
            border: 2px dashed rgba(0,0,0,0.2);
        }

        .add-list:hover {
            background: #e8e8e8;
        }

        .add-list-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-list-plus {
            color: #6a6a6a;
            font-size: 24px;
        }

        .add-list-text {
            color: #6a6a6a;
            font-size: 16px;
            font-weight: 500;
        }

        /* SortableJS Styles */
        .sortable-ghost {
            opacity: 0.5;
            background: #c8c8c8 !important;
        }

        .sortable-chosen {
            transform: rotate(5deg);
        }

        .sortable-drag {
            transform: rotate(5deg);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .column-container {
                width: 300px;
            }
            
            .search-container {
                width: calc(100vw - 280px);
            }
            
            .content-bg {
                width: calc(100vw - 320px);
            }
        }
        
        @media (max-width: 768px) {
            .search-container {
                width: calc(100vw - 60px);
            }
            
            .content-bg {
                width: calc(100vw - 100px);
            }
        }
    </style>

    <div class="container-fluid py-4">
        {{-- Alert --}}
        @include('components.alert')

        <div class="kanban-container">
            <!-- Page Navigation Container - Separated and positioned higher -->
            <div class="page-nav-container">
                <div class="page-nav">
                    <div class="page-nav-light">Boards</div>
                    <div class="page-nav-bold">{{ $current_project->name ?? 'E-Commerce Platform' }}</div>
                </div>
            </div>

            <!-- Back Button Container - Separated from search -->
            <div class="back-btn-container">
                <button class="back-btn" onclick="window.location.href='{{ url('msbrd') }}'">
                    ← Back to Boards
                </button>
            </div>

            <!-- Search Bar Container - Separated from back button -->
            <div class="search-section">
                <div class="search-container">
                    <div class="search-input" onclick="toggleSearch()">
                        <div style="font-size: 16px; color: rgba(52,71,103,0.6);">🔍</div>
                        <span class="search-text">Search tasks...</span>
                    </div>
                </div>
            </div>

            <!-- Content Container -->
            <div class="content-container">
                <div class="content-bg">
                    <!-- Header - overlays on top of the background image -->
                    <div class="header">
                        <h1 class="header-title">{{ $current_project->year ?? date('Y') }} - {{ $current_project->department ?? 'General' }}</h1>
                        <div class="header-actions">
                            <button class="header-btn" onclick="openTaskModal()" title="Add Task">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="#344767" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                                </svg>
                            </button>
                            <button class="header-btn" onclick="refreshBoard()" title="Refresh">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="#344767" d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                                </svg>
                            </button>
                            <button class="header-btn" onclick="showBoardSettings()" title="Settings">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="#344767" d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.82,11.69,4.82,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Kanban Columns -->
                    <div class="kanban-columns">
                        <!-- TO DO Column -->
                        <div class="kanban-column">
                            <div class="column-container">
                                <div class="column-header">
                                    <h2 class="column-title">TO DO</h2>
                                    <svg class="column-menu" fill="none" viewBox="0 0 24 24">
                                        <path fill="#344767" d="M3 0C4.65685 0 6 1.34315 6 3C6 4.65685 4.65685 6 3 6C1.34315 6 0 4.65685 0 3C0 1.34315 1.34315 0 3 0ZM12 0C13.6569 0 15 1.34315 15 3C15 4.65685 13.6569 6 12 6C10.3431 6 9 4.65685 9 3C9 1.34315 10.3431 0 12 0ZM21 0C22.6569 0 24 1.34315 24 3C24 4.65685 22.6569 6 21 6C19.3431 6 18 4.65685 18 3C18 1.34315 19.3431 0 21 0Z"/>
                                    </svg>
                                </div>
                                <div id="todo-cards" class="cards-container">
                                    @if(isset($tasks_by_status) && $tasks_by_status->has('todo'))
                                        @foreach($tasks_by_status->get('todo') as $task)
                                            <div class="card" data-task-id="{{ $task->id }}">
                                                <div class="card-label {{ $task->priority == 'high' || $task->priority == 'urgent' ? 'red' : 'blue' }}"></div>
                                                <div class="card-department">{{ strtoupper($task->department ?? $current_project->department ?? 'GEMBONG') }}</div>
                                                <div class="card-description">{{ $task->title }}</div>
                                                @if($task->due_date)
                                                    <div class="card-date">{{ \Carbon\Carbon::parse($task->due_date)->format('d M y') }}</div>
                                                @endif
                                                <div class="card-footer">
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M9.16667 7.5H12.5C12.721 7.5 12.933 7.5878 13.0893 7.74408C13.2455 7.90036 13.3333 8.11232 13.3333 8.33333C13.3333 8.55435 13.2455 8.76631 13.0893 8.92259C12.933 9.07887 12.721 9.16667 12.5 9.16667H8.33333C8.11232 9.16667 7.90036 9.07887 7.74408 8.92259C7.5878 8.76631 7.5 8.55435 7.5 8.33333V3.33333C7.5 3.11232 7.5878 2.90036 7.74408 2.74408C7.90036 2.5878 8.11232 2.5 8.33333 2.5C8.55435 2.5 8.76631 2.5878 8.92259 2.74408C9.07887 2.90036 9.16667 3.11232 9.16667 3.33333V7.5ZM8.33333 16.6667C3.73083 16.6667 0 12.9358 0 8.33333C0 3.73083 3.73083 0 8.33333 0C12.9358 0 16.6667 3.73083 16.6667 8.33333C16.6667 12.9358 12.9358 16.6667 8.33333 16.6667ZM8.33333 15C10.1014 15 11.7971 14.2976 13.0474 13.0474C14.2976 11.7971 15 10.1014 15 8.33333C15 6.56522 14.2976 4.86953 13.0474 3.61929C11.7971 2.36905 10.1014 1.66667 8.33333 1.66667C6.56522 1.66667 4.86953 2.36905 3.61929 3.61929C2.36905 4.86953 1.66667 6.56522 1.66667 8.33333C1.66667 10.1014 2.36905 11.7971 3.61929 13.0474C4.86953 14.2976 6.56522 15 8.33333 15V15Z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->progress ?? 0 }}%</span>
                                                    </div>
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M2 2h16v2H2V2zm0 4h16v2H2V6zm0 4h11v2H2v-2z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->comments_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="add-card" onclick="openTaskModal('todo')">
                                        <span class="add-card-text">+ Add new card</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IN PROGRESS Column -->
                        <div class="kanban-column">
                            <div class="column-container">
                                <div class="column-header">
                                    <h2 class="column-title">IN PROGRESS</h2>
                                    <svg class="column-menu" fill="none" viewBox="0 0 24 24">
                                        <path fill="#344767" d="M3 0C4.65685 0 6 1.34315 6 3C6 4.65685 4.65685 6 3 6C1.34315 6 0 4.65685 0 3C0 1.34315 1.34315 0 3 0ZM12 0C13.6569 0 15 1.34315 15 3C15 4.65685 13.6569 6 12 6C10.3431 6 9 4.65685 9 3C9 1.34315 10.3431 0 12 0ZM21 0C22.6569 0 24 1.34315 24 3C24 4.65685 22.6569 6 21 6C19.3431 6 18 4.65685 18 3C18 1.34315 19.3431 0 21 0Z"/>
                                    </svg>
                                </div>
                                <div id="progress-cards" class="cards-container">
                                    @if(isset($tasks_by_status) && $tasks_by_status->has('in_progress'))
                                        @foreach($tasks_by_status->get('in_progress') as $task)
                                            <div class="card" data-task-id="{{ $task->id }}">
                                                <div class="card-label {{ $task->priority == 'high' || $task->priority == 'urgent' ? 'red' : 'blue' }}"></div>
                                                <div class="card-department">{{ strtoupper($task->department ?? $current_project->department ?? 'GEMBONG') }}</div>
                                                <div class="card-description">{{ $task->title }}</div>
                                                @if($task->due_date)
                                                    <div class="card-date">{{ \Carbon\Carbon::parse($task->due_date)->format('d M y') }}</div>
                                                @endif
                                                <div class="card-footer">
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M9.16667 7.5H12.5C12.721 7.5 12.933 7.5878 13.0893 7.74408C13.2455 7.90036 13.3333 8.11232 13.3333 8.33333C13.3333 8.55435 13.2455 8.76631 13.0893 8.92259C12.933 9.07887 12.721 9.16667 12.5 9.16667H8.33333C8.11232 9.16667 7.90036 9.07887 7.74408 8.92259C7.5878 8.76631 7.5 8.55435 7.5 8.33333V3.33333C7.5 3.11232 7.5878 2.90036 7.74408 2.74408C7.90036 2.5878 8.11232 2.5 8.33333 2.5C8.55435 2.5 8.76631 2.5878 8.92259 2.74408C9.07887 2.90036 9.16667 3.11232 9.16667 3.33333V7.5ZM8.33333 16.6667C3.73083 16.6667 0 12.9358 0 8.33333C0 3.73083 3.73083 0 8.33333 0C12.9358 0 16.6667 3.73083 16.6667 8.33333C16.6667 12.9358 12.9358 16.6667 8.33333 16.6667ZM8.33333 15C10.1014 15 11.7971 14.2976 13.0474 13.0474C14.2976 11.7971 15 10.1014 15 8.33333C15 6.56522 14.2976 4.86953 13.0474 3.61929C11.7971 2.36905 10.1014 1.66667 8.33333 1.66667C6.56522 1.66667 4.86953 2.36905 3.61929 3.61929C2.36905 4.86953 1.66667 6.56522 1.66667 8.33333C1.66667 10.1014 2.36905 11.7971 3.61929 13.0474C4.86953 14.2976 6.56522 15 8.33333 15V15Z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->progress ?? 0 }}%</span>
                                                    </div>
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M2 2h16v2H2V2zm0 4h16v2H2V6zm0 4h11v2H2v-2z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->comments_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="add-card" onclick="openTaskModal('in_progress')">
                                        <span class="add-card-text">+ Add new card</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- REVIEW Column -->
                        <div class="kanban-column">
                            <div class="column-container">
                                <div class="column-header">
                                    <h2 class="column-title">REVIEW</h2>
                                    <svg class="column-menu" fill="none" viewBox="0 0 24 24">
                                        <path fill="#344767" d="M3 0C4.65685 0 6 1.34315 6 3C6 4.65685 4.65685 6 3 6C1.34315 6 0 4.65685 0 3C0 1.34315 1.34315 0 3 0ZM12 0C13.6569 0 15 1.34315 15 3C15 4.65685 13.6569 6 12 6C10.3431 6 9 4.65685 9 3C9 1.34315 10.3431 0 12 0ZM21 0C22.6569 0 24 1.34315 24 3C24 4.65685 22.6569 6 21 6C19.3431 6 18 4.65685 18 3C18 1.34315 19.3431 0 21 0Z"/>
                                    </svg>
                                </div>
                                <div id="review-cards" class="cards-container">
                                    @if(isset($tasks_by_status) && $tasks_by_status->has('review'))
                                        @foreach($tasks_by_status->get('review') as $task)
                                            <div class="card" data-task-id="{{ $task->id }}">
                                                <div class="card-label {{ $task->priority == 'high' || $task->priority == 'urgent' ? 'red' : 'blue' }}"></div>
                                                <div class="card-department">{{ strtoupper($task->department ?? $current_project->department ?? 'GEMBONG') }}</div>
                                                <div class="card-description">{{ $task->title }}</div>
                                                @if($task->due_date)
                                                    <div class="card-date">{{ \Carbon\Carbon::parse($task->due_date)->format('d M y') }}</div>
                                                @endif
                                                <div class="card-footer">
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M9.16667 7.5H12.5C12.721 7.5 12.933 7.5878 13.0893 7.74408C13.2455 7.90036 13.3333 8.11232 13.3333 8.33333C13.3333 8.55435 13.2455 8.76631 13.0893 8.92259C12.933 9.07887 12.721 9.16667 12.5 9.16667H8.33333C8.11232 9.16667 7.90036 9.07887 7.74408 8.92259C7.5878 8.76631 7.5 8.55435 7.5 8.33333V3.33333C7.5 3.11232 7.5878 2.90036 7.74408 2.74408C7.90036 2.5878 8.11232 2.5 8.33333 2.5C8.55435 2.5 8.76631 2.5878 8.92259 2.74408C9.07887 2.90036 9.16667 3.11232 9.16667 3.33333V7.5ZM8.33333 16.6667C3.73083 16.6667 0 12.9358 0 8.33333C0 3.73083 3.73083 0 8.33333 0C12.9358 0 16.6667 3.73083 16.6667 8.33333C16.6667 12.9358 12.9358 16.6667 8.33333 16.6667ZM8.33333 15C10.1014 15 11.7971 14.2976 13.0474 13.0474C14.2976 11.7971 15 10.1014 15 8.33333C15 6.56522 14.2976 4.86953 13.0474 3.61929C11.7971 2.36905 10.1014 1.66667 8.33333 1.66667C6.56522 1.66667 4.86953 2.36905 3.61929 3.61929C2.36905 4.86953 1.66667 6.56522 1.66667 8.33333C1.66667 10.1014 2.36905 11.7971 3.61929 13.0474C4.86953 14.2976 6.56522 15 8.33333 15V15Z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->progress ?? 0 }}%</span>
                                                    </div>
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M2 2h16v2H2V2zm0 4h16v2H2V6zm0 4h11v2H2v-2z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->comments_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="add-card" onclick="openTaskModal('review')">
                                        <span class="add-card-text">+ Add new card</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DONE Column -->
                        <div class="kanban-column">
                            <div class="column-container">
                                <div class="column-header">
                                    <h2 class="column-title">DONE</h2>
                                    <svg class="column-menu" fill="none" viewBox="0 0 24 24">
                                        <path fill="#344767" d="M3 0C4.65685 0 6 1.34315 6 3C6 4.65685 4.65685 6 3 6C1.34315 6 0 4.65685 0 3C0 1.34315 1.34315 0 3 0ZM12 0C13.6569 0 15 1.34315 15 3C15 4.65685 13.6569 6 12 6C10.3431 6 9 4.65685 9 3C9 1.34315 10.3431 0 12 0ZM21 0C22.6569 0 24 1.34315 24 3C24 4.65685 22.6569 6 21 6C19.3431 6 18 4.65685 18 3C18 1.34315 19.3431 0 21 0Z"/>
                                    </svg>
                                </div>
                                <div id="done-cards" class="cards-container">
                                    @if(isset($tasks_by_status) && $tasks_by_status->has('done'))
                                        @foreach($tasks_by_status->get('done') as $task)
                                            <div class="card" data-task-id="{{ $task->id }}">
                                                <div class="card-label green"></div>
                                                <div class="card-department">{{ strtoupper($task->department ?? $current_project->department ?? 'GEMBONG') }}</div>
                                                <div class="card-description">{{ $task->title }}</div>
                                                @if($task->due_date)
                                                    <div class="card-date">{{ \Carbon\Carbon::parse($task->due_date)->format('d M y') }}</div>
                                                @endif
                                                <div class="card-footer">
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M9.16667 7.5H12.5C12.721 7.5 12.933 7.5878 13.0893 7.74408C13.2455 7.90036 13.3333 8.11232 13.3333 8.33333C13.3333 8.55435 13.2455 8.76631 13.0893 8.92259C12.933 9.07887 12.721 9.16667 12.5 9.16667H8.33333C8.11232 9.16667 7.90036 9.07887 7.74408 8.92259C7.5878 8.76631 7.5 8.55435 7.5 8.33333V3.33333C7.5 3.11232 7.5878 2.90036 7.74408 2.74408C7.90036 2.5878 8.11232 2.5 8.33333 2.5C8.55435 2.5 8.76631 2.5878 8.92259 2.74408C9.07887 2.90036 9.16667 3.11232 9.16667 3.33333V7.5ZM8.33333 16.6667C3.73083 16.6667 0 12.9358 0 8.33333C0 3.73083 3.73083 0 8.33333 0C12.9358 0 16.6667 3.73083 16.6667 8.33333C16.6667 12.9358 12.9358 16.6667 8.33333 16.6667ZM8.33333 15C10.1014 15 11.7971 14.2976 13.0474 13.0474C14.2976 11.7971 15 10.1014 15 8.33333C15 6.56522 14.2976 4.86953 13.0474 3.61929C11.7971 2.36905 10.1014 1.66667 8.33333 1.66667C6.56522 1.66667 4.86953 2.36905 3.61929 3.61929C2.36905 4.86953 1.66667 6.56522 1.66667 8.33333C1.66667 10.1014 2.36905 11.7971 3.61929 13.0474C4.86953 14.2976 6.56522 15 8.33333 15V15Z"/>
                                                        </svg>
                                                        <span class="card-stat-text">100%</span>
                                                    </div>
                                                    <div class="card-stat">
                                                        <svg class="card-stat-icon" fill="none" viewBox="0 0 20 20">
                                                            <path fill="#6a6a6a" d="M2 2h16v2H2V2zm0 4h16v2H2V6zm0 4h11v2H2v-2z"/>
                                                        </svg>
                                                        <span class="card-stat-text">{{ $task->comments_count ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="add-card" onclick="openTaskModal('done')">
                                        <span class="add-card-text">+ Add new card</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Another List -->
                        <div class="kanban-column">
                            <div class="add-list" onclick="addNewList()">
                                <div class="add-list-content">
                                    <span class="add-list-plus">+</span>
                                    <span class="add-list-text">Add another list</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Task Modal --}}
        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="taskForm">
                        <div class="modal-body">
                            <input type="hidden" id="taskId" name="task_id">
                            <input type="hidden" id="taskStatus" name="status">
                            <input type="hidden" id="projectId" name="project_id" value="{{ isset($current_project) ? $current_project->id : 1 }}">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="taskTitle" class="form-control-label">Task Title *</label>
                                        <input type="text" class="form-control" id="taskTitle" name="title" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="taskDescription" class="form-control-label">Description</label>
                                <textarea class="form-control" id="taskDescription" name="description" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="taskPriority" class="form-control-label">Priority</label>
                                        <select class="form-control" id="taskPriority" name="priority">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="taskDueDate" class="form-control-label">Due Date</label>
                                        <input type="date" class="form-control" id="taskDueDate" name="due_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="taskProgress" class="form-control-label">Progress (%)</label>
                                        <input type="number" class="form-control" id="taskProgress" name="progress" min="0" max="100" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="taskAssignee" class="form-control-label">Assign To</label>
                                        @if(isset($all_users))
                                        <select class="form-control" id="taskAssignee" name="assigned_to">
                                            <option value="">Select User</option>
                                            @foreach($all_users as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ trim($user->firstname . ' ' . $user->lastname) ?: $user->username }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input type="text" class="form-control" id="taskAssignee" name="assignee" placeholder="Enter assignee name">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="taskDepartment" class="form-control-label">Department</label>
                                        <input type="text" class="form-control" id="taskDepartment" name="department" placeholder="e.g., GEMBONG" value="{{ $current_project->department ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            @if(isset($categories))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="taskCategory" class="form-control-label">Category</label>
                                        <select class="form-control" id="taskCategory" name="category_id">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveTaskBtn">Save Task</button>
                            <button type="button" class="btn btn-danger" id="deleteTaskBtn" style="display: none;" onclick="deleteTask()">Delete Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
let currentProjectId = {{ isset($current_project) ? $current_project->id : 1 }};

// Initialize enhanced drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize SortableJS for all columns with proper configuration
    const columns = ['todo', 'progress', 'review', 'done'];
    
    columns.forEach(columnName => {
        const columnElement = document.getElementById(columnName + '-cards');
        if (columnElement && window.Sortable) {
            Sortable.create(columnElement, {
                group: 'kanban',
                animation: 150,
                filter: '.add-card',  // Exclude add-card elements from dragging
                preventOnFilter: false,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    const taskId = evt.item.dataset.taskId;
                    const newColumn = evt.to.id.replace('-cards', '');
                    const newPosition = evt.newIndex;
                    
                    // Only process if it's a task card (has taskId)
                    if (taskId) {
                        moveTask(taskId, newColumn, newPosition);
                    }
                }
            });
        }
    });

    // Make add-card elements non-draggable
    document.querySelectorAll('.add-card').forEach(function(element) {
        element.setAttribute('draggable', 'false');
        element.style.cursor = 'pointer';
    });
    
    // Ensure all task cards remain draggable
    document.querySelectorAll('.card[data-task-id]').forEach(function(element) {
        element.setAttribute('draggable', 'true');
        element.style.cursor = 'move';
    });

    console.log('Enhanced Kanban board initialized successfully!');
});

// Toggle search functionality
function toggleSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchText = document.querySelector('.search-text');
    
    if (searchText.textContent === 'Search tasks...') {
        searchText.innerHTML = '<input type="text" style="background: transparent; border: none; outline: none; color: #344767; font-size: 14px; width: 100%;" placeholder="Type to search..." onkeyup="searchTasks(this.value)" autofocus>';
    } else {
        searchText.textContent = 'Search tasks...';
        // Clear search
        searchTasks('');
    }
}

// Search tasks
function searchTasks(searchTerm = '') {
    const taskCards = document.querySelectorAll('.card[data-task-id]');
    
    taskCards.forEach(card => {
        const description = card.querySelector('.card-description').textContent.toLowerCase();
        const department = card.querySelector('.card-department').textContent.toLowerCase();
        
        if (searchTerm === '' || description.includes(searchTerm.toLowerCase()) || department.includes(searchTerm.toLowerCase())) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Open task modal - Fixed to work with both header button and add-card buttons
function openTaskModal(status = null, taskId = null) {
    const modal = new bootstrap.Modal(document.getElementById('taskModal'));
    const form = document.getElementById('taskForm');
    const modalTitle = document.getElementById('taskModalLabel');
    const deleteBtn = document.getElementById('deleteTaskBtn');
    
    // Reset form
    form.reset();
    document.getElementById('projectId').value = currentProjectId;
    
    if (taskId) {
        // Edit mode
        modalTitle.textContent = 'Edit Task';
        deleteBtn.style.display = 'inline-block';
        loadTaskData(taskId);
    } else {
        // Add mode
        modalTitle.textContent = 'Add New Task';
        deleteBtn.style.display = 'none';
        document.getElementById('taskId').value = '';
        if (status) {
            document.getElementById('taskStatus').value = status;
        } else {
            // Default to 'todo' if no status specified (for header button)
            document.getElementById('taskStatus').value = 'todo';
        }
    }
    
    modal.show();
}

// Load task data for editing
function loadTaskData(taskId) {
    fetch(`{{ url('msbrd/api/get-task') }}?task_id=${taskId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const task = data.task;
                document.getElementById('taskId').value = task.id;
                document.getElementById('taskTitle').value = task.title;
                document.getElementById('taskDescription').value = task.description || '';
                document.getElementById('taskStatus').value = task.status;
                document.getElementById('taskPriority').value = task.priority;
                document.getElementById('taskDueDate').value = task.due_date || '';
                if (document.getElementById('taskCategory')) {
                    document.getElementById('taskCategory').value = task.category_id || '';
                }
                if (document.getElementById('taskAssignee')) {
                    document.getElementById('taskAssignee').value = task.assigned_to || '';
                }
                document.getElementById('taskProgress').value = task.progress || 0;
            }
        })
        .catch(error => {
            console.error('Error loading task:', error);
            alert('Error loading task data');
        });
}

// Save task - Enhanced with better error handling
document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const taskId = document.getElementById('taskId').value;
    const isEdit = taskId !== '';
    
    const url = isEdit ? '{{ url("msbrd/api/update-task") }}' : '{{ url("msbrd/api/create-task") }}';
    
    // Show loading state
    const saveBtn = document.getElementById('saveTaskBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Task saved successfully!');
            bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert(data.error || 'Error saving task');
        }
    })
    .catch(error => {
        console.error('Error saving task:', error);
        alert('Error saving task. Please try again.');
    })
    .finally(() => {
        // Reset button state
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
});

// Move task
function moveTask(taskId, newColumn, newPosition) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('new_column', newColumn);
    formData.append('new_position', newPosition);
    
    fetch('{{ url("msbrd/api/move-task") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Task moved successfully');
        } else {
            alert(data.error || 'Error moving task');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error moving task:', error);
        alert('Error moving task');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
}

// Delete task
function deleteTask() {
    const taskId = document.getElementById('taskId').value;
    
    if (!taskId) return;
    
    if (confirm('Are you sure you want to delete this task?')) {
        fetch(`{{ url('msbrd/api/delete-task') }}?task_id=${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert(data.error || 'Error deleting task');
            }
        })
        .catch(error => {
            console.error('Error deleting task:', error);
            alert('Error deleting task');
        });
    }
}

// Refresh board
function refreshBoard() {
    window.location.reload();
}

// Show board settings
function showBoardSettings() {
    alert('Board settings feature coming soon!');
}

// Add new list
function addNewList() {
    alert('Add another list functionality would go here!');
}

// Task card click handler - Enhanced to work properly
document.addEventListener('click', function(e) {
    const taskCard = e.target.closest('.card[data-task-id]');
    if (taskCard && !e.target.closest('.add-card')) {
        const taskId = taskCard.dataset.taskId;
        if (taskId) {
            console.log('Task card clicked, opening detail modal for task ID:', taskId);
            openTaskDetailModal(taskId);
        }
    }
});
</script>
@endpush

{{-- Include Task Detail Modal --}}
@include('kanban.msbrd.modals.task-detail-modal')

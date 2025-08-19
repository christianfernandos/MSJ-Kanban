@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Kanban Settings'])
    <div class="container-fluid py-4">
        {{-- Alert --}}
        @include('components.alert')
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">Kanban Settings</h5>
                                <p class="text-sm mb-0">Configure your Kanban board settings and preferences</p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">
                                    <button class="btn btn-outline-primary btn-sm mb-0" onclick="resetSettings()">
                                        <i class="fas fa-undo me-1"></i> Reset to Default
                                    </button>
                                    <button class="btn btn-outline-info btn-sm mb-0" onclick="exportSettings()">
                                        <i class="fas fa-download me-1"></i> Export Settings
                                    </button>
                                    <button class="btn btn-outline-success btn-sm mb-0" onclick="document.getElementById('importFile').click()">
                                        <i class="fas fa-upload me-1"></i> Import Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ url($url_menu . '/update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            {{-- General Settings --}}
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">General Settings</h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="default_task_status" class="form-control-label">Default Task Status</label>
                                        <select class="form-control" id="default_task_status" name="default_task_status" required>
                                            @foreach($status_options as $value => $label)
                                                <option value="{{ $value }}" {{ ($kanban_settings['default_task_status'] ?? 'todo') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Default status for new tasks</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="default_task_priority" class="form-control-label">Default Task Priority</label>
                                        <select class="form-control" id="default_task_priority" name="default_task_priority" required>
                                            @foreach($priority_options as $value => $label)
                                                <option value="{{ $value }}" {{ ($kanban_settings['default_task_priority'] ?? 'medium') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Default priority for new tasks</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="task_due_date_reminder" class="form-control-label">Due Date Reminder (Days)</label>
                                        <input type="number" class="form-control" id="task_due_date_reminder" name="task_due_date_reminder" 
                                               value="{{ $kanban_settings['task_due_date_reminder'] ?? 3 }}" min="1" max="30" required>
                                        <small class="form-text text-muted">Days before due date to send reminder</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_tasks_per_column" class="form-control-label">Max Tasks per Column</label>
                                        <input type="number" class="form-control" id="max_tasks_per_column" name="max_tasks_per_column" 
                                               value="{{ $kanban_settings['max_tasks_per_column'] ?? 50 }}" min="10" max="200" required>
                                        <small class="form-text text-muted">Maximum number of tasks allowed per column</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="board_theme" class="form-control-label">Board Theme</label>
                                        <select class="form-control" id="board_theme" name="board_theme" required>
                                            @foreach($theme_options as $value => $label)
                                                <option value="{{ $value }}" {{ ($kanban_settings['board_theme'] ?? 'default') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Visual theme for Kanban board</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            {{-- Feature Settings --}}
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-uppercase text-body text-xs font-weight-bolder">Feature Settings</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="auto_assign_tasks" name="auto_assign_tasks" 
                                               {{ ($kanban_settings['auto_assign_tasks'] ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_assign_tasks">Auto Assign Tasks</label>
                                        <small class="form-text text-muted d-block">Automatically assign tasks to project members</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                               {{ ($kanban_settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">Email Notifications</label>
                                        <small class="form-text text-muted d-block">Send email notifications for task updates</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_task_comments" name="allow_task_comments" 
                                               {{ ($kanban_settings['allow_task_comments'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_task_comments">Allow Task Comments</label>
                                        <small class="form-text text-muted d-block">Enable commenting on tasks</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allow_task_attachments" name="allow_task_attachments" 
                                               {{ ($kanban_settings['allow_task_attachments'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_task_attachments">Allow Task Attachments</label>
                                        <small class="form-text text-muted d-block">Enable file attachments on tasks</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_task_numbers" name="show_task_numbers" 
                                               {{ ($kanban_settings['show_task_numbers'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_task_numbers">Show Task Numbers</label>
                                        <small class="form-text text-muted d-block">Display task ID numbers on cards</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_time_tracking" name="enable_time_tracking" 
                                               {{ ($kanban_settings['enable_time_tracking'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_time_tracking">Enable Time Tracking</label>
                                        <small class="form-text text-muted d-block">Allow time tracking on tasks</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark">

                            {{-- Action Buttons --}}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Save Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Hidden Import Form --}}
                        <form id="importForm" method="POST" action="{{ url($url_menu . '/import') }}" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" id="importFile" name="settings_file" accept=".json" onchange="document.getElementById('importForm').submit()">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Settings Preview Card --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Current Settings Preview</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-horizontal">
                                    <div class="icon icon-shape icon-xs rounded-circle bg-gradient-info shadow text-center">
                                        <i class="ni ni-settings-gear-65 opacity-10"></i>
                                    </div>
                                    <div class="description ps-3">
                                        <p class="mb-0"><strong>Default Task Status:</strong> {{ ucfirst(str_replace('_', ' ', $kanban_settings['default_task_status'] ?? 'todo')) }}</p>
                                        <p class="mb-0"><strong>Default Priority:</strong> {{ ucfirst($kanban_settings['default_task_priority'] ?? 'medium') }}</p>
                                        <p class="mb-0"><strong>Due Date Reminder:</strong> {{ $kanban_settings['task_due_date_reminder'] ?? 3 }} days</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-horizontal">
                                    <div class="icon icon-shape icon-xs rounded-circle bg-gradient-success shadow text-center">
                                        <i class="ni ni-app opacity-10"></i>
                                    </div>
                                    <div class="description ps-3">
                                        <p class="mb-0"><strong>Max Tasks per Column:</strong> {{ $kanban_settings['max_tasks_per_column'] ?? 50 }}</p>
                                        <p class="mb-0"><strong>Board Theme:</strong> {{ ucfirst($kanban_settings['board_theme'] ?? 'default') }}</p>
                                        <p class="mb-0"><strong>Time Tracking:</strong> {{ ($kanban_settings['enable_time_tracking'] ?? true) ? 'Enabled' : 'Disabled' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function resetSettings() {
        Swal.fire({
            title: 'Reset Settings',
            text: 'Are you sure you want to reset all settings to default values?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reset',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ url($url_menu . "/reset") }}';
            }
        });
    }

    function exportSettings() {
        window.location.href = '{{ url($url_menu . "/export") }}';
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['default_task_status', 'default_task_priority', 'task_due_date_reminder', 'max_tasks_per_column', 'board_theme'];
        let isValid = true;

        requiredFields.forEach(function(field) {
            const input = document.getElementById(field);
            if (!input.value) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error',
                confirmButtonColor: '#028284'
            });
        }
    });

    // Real-time preview updates
    document.querySelectorAll('input, select').forEach(function(element) {
        element.addEventListener('change', function() {
            // You can add real-time preview updates here if needed
        });
    });
</script>
@endpush
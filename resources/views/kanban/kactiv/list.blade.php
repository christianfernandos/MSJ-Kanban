@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Activity Logs'])
    <div class="container-fluid py-4">
        {{-- Alert --}}
        @include('components.alert')
        
        {{-- Activity Filters --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">Activity Logs</h5>
                                <p class="text-sm mb-0">Track all activities and changes in your Kanban projects</p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">
                                    <button class="btn btn-outline-primary btn-sm mb-0" onclick="exportActivities()">
                                        <i class="fas fa-download me-1"></i> Export
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm mb-0" onclick="clearFilters()">
                                        <i class="fas fa-times me-1"></i> Clear Filters
                                    </button>
                                    <button class="btn btn-primary btn-sm mb-0" onclick="refreshActivities()">
                                        <i class="fas fa-sync me-1"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="activity_type" class="form-control-label">Activity Type</label>
                                    <select class="form-control" id="activity_type" name="activity_type" onchange="filterActivities()">
                                        <option value="">All Activities</option>
                                        <option value="create_task">Task Created</option>
                                        <option value="update_task">Task Updated</option>
                                        <option value="move_task">Task Moved</option>
                                        <option value="delete_task">Task Deleted</option>
                                        <option value="create_project">Project Created</option>
                                        <option value="update_project">Project Updated</option>
                                        <option value="add_comment">Comment Added</option>
                                        <option value="add_attachment">Attachment Added</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_filter" class="form-control-label">User</label>
                                    <select class="form-control" id="user_filter" name="user_filter" onchange="filterActivities()">
                                        <option value="">All Users</option>
                                        {{-- Users will be loaded dynamically --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_from" class="form-control-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" onchange="filterActivities()" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_to" class="form-control-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" onchange="filterActivities()" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Statistics --}}
        <div class="row mt-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Activities</p>
                                    <h5 class="font-weight-bolder mb-0" id="todayActivities">24</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-calendar-grid-58 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">This Week</p>
                                    <h5 class="font-weight-bolder mb-0" id="weekActivities">156</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Most Active User</p>
                                    <h5 class="font-weight-bolder mb-0" id="mostActiveUser">John Doe</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="ni ni-single-02 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Activities</p>
                                    <h5 class="font-weight-bolder mb-0" id="totalActivities">1,247</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="ni ni-bullet-list-67 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="row mt-4">
            <div class="col-lg-8 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Recent Activities</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="timeline timeline-one-side" id="activityTimeline">
                            {{-- Sample activities - these would be loaded dynamically --}}
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-check-bold text-success text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Task Completed</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <strong>John Doe</strong> completed task "Update user interface"
                                    </p>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        <i class="ni ni-time-alarm"></i> 2 minutes ago
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-single-copy-04 text-info text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">New Task Created</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <strong>Jane Smith</strong> created task "Design mobile layout"
                                    </p>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        <i class="ni ni-time-alarm"></i> 15 minutes ago
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-app text-warning text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Task Moved</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <strong>Mike Johnson</strong> moved task "Fix login bug" from "To Do" to "In Progress"
                                    </p>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        <i class="ni ni-time-alarm"></i> 1 hour ago
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-chat-round text-primary text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Comment Added</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <strong>Sarah Wilson</strong> commented on task "Database optimization"
                                    </p>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        <i class="ni ni-time-alarm"></i> 2 hours ago
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-collection text-success text-gradient"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">Project Created</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        <strong>Admin</strong> created new project "Mobile App Development"
                                    </p>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        <i class="ni ni-time-alarm"></i> 3 hours ago
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Load More Button --}}
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-primary btn-sm" onclick="loadMoreActivities()">
                                <i class="fas fa-plus me-1"></i> Load More Activities
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Activity Chart --}}
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>Activity Distribution</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="activityChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Activity Table --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Detailed Activity Log</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-3">
                            <table class="table align-items-center mb-0" id="activityTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Activity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">User</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Time</th>
                                        <th class="text-secondary opacity-7">Details</th>
                                    </tr>
                                </thead>
                                <tbody id="activityTableBody">
                                    {{-- Sample data - would be loaded dynamically --}}
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Task Completed</h6>
                                                    <p class="text-xs text-secondary mb-0">Task status changed</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('assets/img/team-2.jpg') }}" class="avatar avatar-sm me-3">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">John Doe</h6>
                                                    <p class="text-xs text-secondary mb-0">Developer</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Completed task "Update user interface"</p>
                                            <p class="text-xs text-secondary mb-0">Project: Website Redesign</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('H:i A') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" onclick="viewActivityDetails(1)">
                                                <i class="fa fa-eye text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Task Created</h6>
                                                    <p class="text-xs text-secondary mb-0">New task added</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('assets/img/team-3.jpg') }}" class="avatar avatar-sm me-3">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Jane Smith</h6>
                                                    <p class="text-xs text-secondary mb-0">Project Manager</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Created task "Design mobile layout"</p>
                                            <p class="text-xs text-secondary mb-0">Project: Mobile App</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('H:i A', strtotime('-15 minutes')) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" onclick="viewActivityDetails(2)">
                                                <i class="fa fa-eye text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
    // Initialize Activity Chart
    var ctx = document.getElementById("activityChart").getContext("2d");
    
    new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Task Updates", "Comments", "Attachments", "Projects", "Others"],
            datasets: [{
                label: "Activities",
                data: [45, 25, 15, 10, 5],
                backgroundColor: [
                    "rgba(40, 167, 69, 0.8)",
                    "rgba(23, 162, 184, 0.8)",
                    "rgba(255, 193, 7, 0.8)",
                    "rgba(220, 53, 69, 0.8)",
                    "rgba(108, 117, 125, 0.8)"
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Initialize DataTable
    $(document).ready(function() {
        $('#activityTable').DataTable({
            "language": {
                "search": "Search Activities:",
                "lengthMenu": "Show _MENU_ activities",
                "zeroRecords": "No activities found",
                "info": "Showing _START_ to _END_ of _TOTAL_ activities",
                "infoEmpty": "No activities available",
                "infoFiltered": "(filtered from _MAX_ total activities)"
            },
            responsive: true,
            order: [[3, 'desc'], [4, 'desc']], // Sort by date and time descending
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ]
        });
    });

    function filterActivities() {
        // Get filter values
        const activityType = document.getElementById('activity_type').value;
        const userFilter = document.getElementById('user_filter').value;
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;

        // Show loading
        Swal.fire({
            title: 'Filtering Activities...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate API call
        setTimeout(() => {
            Swal.close();
            // Reload activities with filters
            loadActivities({
                type: activityType,
                user: userFilter,
                date_from: dateFrom,
                date_to: dateTo
            });
        }, 1000);
    }

    function loadActivities(filters = {}) {
        // This would typically make an AJAX call to load activities
        console.log('Loading activities with filters:', filters);
        
        // Update statistics
        updateActivityStats();
        
        // Reload DataTable
        $('#activityTable').DataTable().ajax.reload();
    }

    function updateActivityStats() {
        // Update activity statistics
        document.getElementById('todayActivities').textContent = Math.floor(Math.random() * 50) + 10;
        document.getElementById('weekActivities').textContent = Math.floor(Math.random() * 200) + 100;
        document.getElementById('totalActivities').textContent = (Math.floor(Math.random() * 1000) + 1000).toLocaleString();
    }

    function clearFilters() {
        document.getElementById('activity_type').value = '';
        document.getElementById('user_filter').value = '';
        document.getElementById('date_from').value = '{{ date("Y-m-d", strtotime("-7 days")) }}';
        document.getElementById('date_to').value = '{{ date("Y-m-d") }}';
        
        filterActivities();
    }

    function refreshActivities() {
        filterActivities();
    }

    function loadMoreActivities() {
        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
        button.disabled = true;

        // Simulate loading more activities
        setTimeout(() => {
            // Add more activities to timeline
            const timeline = document.getElementById('activityTimeline');
            const newActivity = `
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="ni ni-attach-87 text-info text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">Attachment Added</h6>
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            <strong>Alex Brown</strong> added attachment to task "Documentation update"
                        </p>
                        <p class="text-secondary text-xs mt-1 mb-0">
                            <i class="ni ni-time-alarm"></i> 4 hours ago
                        </p>
                    </div>
                </div>
            `;
            
            timeline.insertAdjacentHTML('beforeend', newActivity);
            
            // Restore button
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1000);
    }

    function exportActivities() {
        Swal.fire({
            title: 'Export Activities',
            text: 'Choose export format',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Excel',
            cancelButtonText: 'PDF',
            showDenyButton: true,
            denyButtonText: 'CSV'
        }).then((result) => {
            if (result.isConfirmed) {
                exportToFormat('excel');
            } else if (result.isDenied) {
                exportToFormat('csv');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                exportToFormat('pdf');
            }
        });
    }

    function exportToFormat(format) {
        Swal.fire({
            title: 'Exporting...',
            text: `Preparing ${format.toUpperCase()} export`,
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            Swal.close();
            Swal.fire({
                title: 'Export Complete!',
                text: `Activities exported to ${format.toUpperCase()} format`,
                icon: 'success',
                confirmButtonColor: '#028284'
            });
        }, 2000);
    }

    function viewActivityDetails(activityId) {
        Swal.fire({
            title: 'Activity Details',
            html: `
                <div class="text-left">
                    <p><strong>Activity ID:</strong> ${activityId}</p>
                    <p><strong>Type:</strong> Task Completed</p>
                    <p><strong>User:</strong> John Doe</p>
                    <p><strong>Description:</strong> Completed task "Update user interface"</p>
                    <p><strong>Project:</strong> Website Redesign</p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Time:</strong> ${new Date().toLocaleTimeString()}</p>
                    <p><strong>IP Address:</strong> 192.168.1.100</p>
                    <p><strong>User Agent:</strong> Chrome/91.0.4472.124</p>
                </div>
            `,
            width: 600,
            confirmButtonColor: '#028284'
        });
    }
</script>
@endpush
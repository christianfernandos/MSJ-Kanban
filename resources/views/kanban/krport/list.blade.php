@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Kanban Reports'])
    <div class="container-fluid py-4">
        {{-- Alert --}}
        @include('components.alert')
        
        {{-- Report Filters --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">Kanban Reports</h5>
                                <p class="text-sm mb-0">Generate comprehensive reports for your Kanban projects</p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <div class="ms-auto my-auto">
                                    <button class="btn btn-outline-primary btn-sm mb-0" onclick="exportReport('excel')">
                                        <i class="fas fa-file-excel me-1"></i> Export Excel
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm mb-0" onclick="exportReport('pdf')">
                                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                                    </button>
                                    <button class="btn btn-primary btn-sm mb-0" onclick="generateReport()">
                                        <i class="fas fa-chart-bar me-1"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="reportForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="report_type" class="form-control-label">Report Type</label>
                                        <select class="form-control" id="report_type" name="report_type">
                                            <option value="summary">Project Summary</option>
                                            <option value="task_status">Task Status Report</option>
                                            <option value="productivity">Productivity Report</option>
                                            <option value="time_tracking">Time Tracking Report</option>
                                            <option value="member_performance">Member Performance</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_from" class="form-control-label">Date From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ date('Y-m-01') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_to" class="form-control-label">Date To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="project_filter" class="form-control-label">Project Filter</label>
                                        <select class="form-control" id="project_filter" name="project_filter">
                                            <option value="">All Projects</option>
                                            {{-- Projects will be loaded dynamically --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Results --}}
        <div class="row mt-4" id="reportResults" style="display: none;">
            {{-- Summary Cards --}}
            <div class="col-12">
                <div class="row" id="summaryCards">
                    {{-- Cards will be populated dynamically --}}
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="col-lg-8 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 id="chartTitle">Report Chart</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="reportChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Report Details --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Report Details</h6>
                    </div>
                    <div class="card-body p-3">
                        <div id="reportDetails">
                            {{-- Details will be populated dynamically --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="col-12 mt-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 id="tableTitle">Report Data</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-3">
                            <table class="table align-items-center mb-0" id="reportTable">
                                <thead id="reportTableHead">
                                    {{-- Table headers will be populated dynamically --}}
                                </thead>
                                <tbody id="reportTableBody">
                                    {{-- Table data will be populated dynamically --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Reports --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Quick Reports</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/icons/flags/US.png') }}" alt="Country flag">
                                    <h6 class="mb-0">Today's Tasks</h6>
                                    <p class="text-sm mb-0">Tasks due today</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/icons/flags/DE.png') }}" alt="Country flag">
                                    <h6 class="mb-0">Overdue Tasks</h6>
                                    <p class="text-sm mb-0">Tasks past due date</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/icons/flags/GB.png') }}" alt="Country flag">
                                    <h6 class="mb-0">Completed This Week</h6>
                                    <p class="text-sm mb-0">Tasks completed this week</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                    <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/icons/flags/BR.png') }}" alt="Country flag">
                                    <h6 class="mb-0">Team Performance</h6>
                                    <p class="text-sm mb-0">Overall team metrics</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scheduled Reports --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h6>Scheduled Reports</h6>
                                <p class="text-sm mb-0">Automatically generated reports</p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                                <button class="btn btn-outline-primary btn-sm mb-0" onclick="scheduleReport()">
                                    <i class="fas fa-plus me-1"></i> Schedule New Report
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-3">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Report Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Frequency</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Next Run</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-secondary opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Weekly Summary</h6>
                                                    <p class="text-xs text-secondary mb-0">Project progress summary</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Summary</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-success">Weekly</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('M d, Y', strtotime('next monday')) }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" onclick="editScheduledReport(1)">
                                                <i class="fa fa-edit text-xs"></i>
                                            </button>
                                            <button class="btn btn-link text-danger mb-0" onclick="deleteScheduledReport(1)">
                                                <i class="fa fa-trash text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Monthly Performance</h6>
                                                    <p class="text-xs text-secondary mb-0">Team performance metrics</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Performance</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">Monthly</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ date('M d, Y', strtotime('first day of next month')) }}</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" onclick="editScheduledReport(2)">
                                                <i class="fa fa-edit text-xs"></i>
                                            </button>
                                            <button class="btn btn-link text-danger mb-0" onclick="deleteScheduledReport(2)">
                                                <i class="fa fa-trash text-xs"></i>
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
    let reportChart = null;

    function generateReport() {
        const formData = new FormData(document.getElementById('reportForm'));
        const reportType = formData.get('report_type');
        
        // Show loading
        Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait while we generate your report',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate API call
        setTimeout(() => {
            Swal.close();
            showReportResults(reportType);
        }, 2000);
    }

    function showReportResults(reportType) {
        document.getElementById('reportResults').style.display = 'block';
        
        // Update titles
        document.getElementById('chartTitle').textContent = getReportTitle(reportType);
        document.getElementById('tableTitle').textContent = getReportTitle(reportType) + ' - Detailed Data';
        
        // Generate sample data based on report type
        generateSampleData(reportType);
        
        // Scroll to results
        document.getElementById('reportResults').scrollIntoView({ behavior: 'smooth' });
    }

    function getReportTitle(reportType) {
        const titles = {
            'summary': 'Project Summary Report',
            'task_status': 'Task Status Report',
            'productivity': 'Productivity Report',
            'time_tracking': 'Time Tracking Report',
            'member_performance': 'Member Performance Report'
        };
        return titles[reportType] || 'Report';
    }

    function generateSampleData(reportType) {
        // Generate summary cards
        generateSummaryCards(reportType);
        
        // Generate chart
        generateChart(reportType);
        
        // Generate details
        generateDetails(reportType);
        
        // Generate table
        generateTable(reportType);
    }

    function generateSummaryCards(reportType) {
        const summaryCards = document.getElementById('summaryCards');
        let cardsHtml = '';
        
        if (reportType === 'summary') {
            cardsHtml = `
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Projects</p>
                                        <h5 class="font-weight-bolder mb-0">12</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="ni ni-collection text-lg opacity-10"></i>
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
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Completed Tasks</p>
                                        <h5 class="font-weight-bolder mb-0">156</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                        <i class="ni ni-check-bold text-lg opacity-10"></i>
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
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">In Progress</p>
                                        <h5 class="font-weight-bolder mb-0">43</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                        <i class="ni ni-time-alarm text-lg opacity-10"></i>
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
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Overdue</p>
                                        <h5 class="font-weight-bolder mb-0">8</h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                        <i class="ni ni-alert-circle text-lg opacity-10"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        summaryCards.innerHTML = cardsHtml;
    }

    function generateChart(reportType) {
        const ctx = document.getElementById("reportChart").getContext("2d");
        
        // Destroy existing chart if it exists
        if (reportChart) {
            reportChart.destroy();
        }
        
        let chartData = {};
        
        if (reportType === 'summary') {
            chartData = {
                type: "doughnut",
                data: {
                    labels: ["Completed", "In Progress", "To Do", "Review"],
                    datasets: [{
                        label: "Tasks",
                        data: [156, 43, 28, 15],
                        backgroundColor: [
                            "rgba(40, 167, 69, 0.8)",
                            "rgba(23, 162, 184, 0.8)",
                            "rgba(108, 117, 125, 0.8)",
                            "rgba(255, 193, 7, 0.8)"
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
            };
        } else if (reportType === 'productivity') {
            chartData = {
                type: "line",
                data: {
                    labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
                    datasets: [{
                        label: "Tasks Completed",
                        data: [12, 19, 15, 25],
                        borderColor: "rgba(40, 167, 69, 1)",
                        backgroundColor: "rgba(40, 167, 69, 0.1)",
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
        }
        
        reportChart = new Chart(ctx, chartData);
    }

    function generateDetails(reportType) {
        const details = document.getElementById('reportDetails');
        let detailsHtml = '';
        
        if (reportType === 'summary') {
            detailsHtml = `
                <div class="timeline timeline-one-side">
                    <div class="timeline-block mb-3">
                        <span class="timeline-step">
                            <i class="ni ni-check-bold text-success"></i>
                        </span>
                        <div class="timeline-content">
                            <h6 class="text-dark text-sm font-weight-bold mb-0">Completion Rate</h6>
                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">78.4% of tasks completed on time</p>
                        </div>
                    </div>
                    <div class="timeline-block mb-3">
                        <span class="timeline-step">
                            <i class="ni ni-time-alarm text-info"></i>
                        </span>
                        <div class="timeline-content">
                            <h6 class="text-dark text-sm font-weight-bold mb-0">Average Time</h6>
                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">3.2 days per task</p>
                        </div>
                    </div>
                    <div class="timeline-block mb-3">
                        <span class="timeline-step">
                            <i class="ni ni-single-02 text-warning"></i>
                        </span>
                        <div class="timeline-content">
                            <h6 class="text-dark text-sm font-weight-bold mb-0">Team Performance</h6>
                            <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">85% efficiency rating</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        details.innerHTML = detailsHtml;
    }

    function generateTable(reportType) {
        const tableHead = document.getElementById('reportTableHead');
        const tableBody = document.getElementById('reportTableBody');
        
        if (reportType === 'summary') {
            tableHead.innerHTML = `
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Tasks</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Completed</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Progress</th>
                </tr>
            `;
            
            tableBody.innerHTML = `
                <tr>
                    <td><h6 class="mb-0 text-sm">Website Redesign</h6></td>
                    <td><p class="text-sm font-weight-bold mb-0">45</p></td>
                    <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">38</span></td>
                    <td class="align-middle text-center">
                        <div class="progress-wrapper w-75 mx-auto">
                            <div class="progress-info">
                                <div class="progress-percentage">
                                    <span class="text-xs font-weight-bold">84%</span>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-gradient-success w-84" role="progressbar"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><h6 class="mb-0 text-sm">Mobile App</h6></td>
                    <td><p class="text-sm font-weight-bold mb-0">32</p></td>
                    <td class="align-middle text-center"><span class="text-secondary text-xs font-weight-bold">24</span></td>
                    <td class="align-middle text-center">
                        <div class="progress-wrapper w-75 mx-auto">
                            <div class="progress-info">
                                <div class="progress-percentage">
                                    <span class="text-xs font-weight-bold">75%</span>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-gradient-info w-75" role="progressbar"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    function exportReport(format) {
        Swal.fire({
            title: 'Exporting Report...',
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
                text: `Report has been exported to ${format.toUpperCase()} format`,
                icon: 'success',
                confirmButtonColor: '#028284'
            });
        }, 2000);
    }

    function scheduleReport() {
        Swal.fire({
            title: 'Schedule New Report',
            html: `
                <div class="form-group">
                    <label>Report Name</label>
                    <input type="text" id="scheduleName" class="form-control" placeholder="Enter report name">
                </div>
                <div class="form-group">
                    <label>Report Type</label>
                    <select id="scheduleType" class="form-control">
                        <option value="summary">Project Summary</option>
                        <option value="productivity">Productivity Report</option>
                        <option value="performance">Performance Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Frequency</label>
                    <select id="scheduleFrequency" class="form-control">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Schedule Report',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#028284'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Report Scheduled!',
                    text: 'Your report has been scheduled successfully',
                    icon: 'success',
                    confirmButtonColor: '#028284'
                });
            }
        });
    }

    function editScheduledReport(id) {
        Swal.fire({
            title: 'Edit Scheduled Report',
            text: 'Edit scheduled report functionality would be implemented here',
            icon: 'info',
            confirmButtonColor: '#028284'
        });
    }

    function deleteScheduledReport(id) {
        Swal.fire({
            title: 'Delete Scheduled Report',
            text: 'Are you sure you want to delete this scheduled report?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Scheduled report has been deleted',
                    icon: 'success',
                    confirmButtonColor: '#028284'
                });
            }
        });
    }

    // Initialize DataTable for report table
    document.addEventListener('DOMContentLoaded', function() {
        // Load projects for filter
        // This would typically be an AJAX call to get projects
    });
</script>
@endpush
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Kanban Boards'])
    
    <div class="container-fluid py-4">
        {{-- Alert --}}
        @include('components.alert')
        
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-white text-sm mb-0 text-capitalize font-weight-bold">Kanban Management</p>
                                    <h5 class="text-white font-weight-bolder mb-0">
                                        Project Boards
                                    </h5>
                                    <p class="text-white text-sm mb-0 opacity-8">Manage your projects with visual boards</p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                    <i class="ni ni-collection text-lg opacity-10 text-primary" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="searchInput" placeholder="Search boards...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-primary" onclick="openCreateBoardModal()">
                                    <i class="fas fa-plus me-2"></i>Create New Board
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recently Viewed Section --}}
        @if(isset($recently_viewed) && $recently_viewed->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Recently Viewed</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="recently-viewed">
                            @foreach($recently_viewed as $project)
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card board-card h-100" data-project-id="{{ $project->id }}" onclick="openBoard({{ $project->id }})">
                                        <div class="card-header board-header bg-gradient-{{ $loop->index % 4 == 0 ? 'primary' : ($loop->index % 4 == 1 ? 'success' : ($loop->index % 4 == 2 ? 'info' : 'warning')) }} p-3">
                                            <div class="board-pattern"></div>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="mb-2">{{ $project->name }}</h6>
                                            <p class="text-sm text-muted mb-3 flex-grow-1">{{ Str::limit($project->description, 80) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $project->updated_at->diffForHumans() }}
                                                </small>
                                                <i class="fas fa-arrow-right text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- My Boards Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>My Boards</h6>
                            <span class="badge badge-sm bg-gradient-secondary">{{ isset($my_boards) ? $my_boards->count() : 0 }} boards</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="my-boards">
                            @if(isset($my_boards))
                                @foreach($my_boards as $project)
                                    <div class="col-xl-3 col-md-6 mb-4">
                                        <div class="card board-card h-100" data-project-id="{{ $project->id }}" onclick="openBoard({{ $project->id }})">
                                            <div class="card-header board-header bg-gradient-{{ $loop->index % 4 == 0 ? 'success' : ($loop->index % 4 == 1 ? 'info' : ($loop->index % 4 == 2 ? 'warning' : 'primary')) }} p-3">
                                                <div class="board-pattern"></div>
                                            </div>
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="mb-2">{{ $project->name }}</h6>
                                                <p class="text-sm text-muted mb-3 flex-grow-1">{{ Str::limit($project->description, 80) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-sm bg-gradient-{{ $project->status == 'active' ? 'success' : 'secondary' }} me-2">
                                                            {{ ucfirst($project->status) }}
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $project->tasks_count ?? 0 }} tasks
                                                        </small>
                                                    </div>
                                                    <i class="fas fa-arrow-right text-primary"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            
                            {{-- Create New Board Card --}}
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card create-board-card h-100" onclick="openCreateBoardModal()">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md mb-3">
                                            <i class="fas fa-plus text-lg opacity-10" aria-hidden="true"></i>
                                        </div>
                                        <h6 class="text-primary mb-2">Create New Board</h6>
                                        <p class="text-sm text-muted mb-0">Start organizing your project</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shared Boards Section --}}
        @if(isset($shared_boards) && $shared_boards->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Shared with You</h6>
                            <span class="badge badge-sm bg-gradient-info">{{ $shared_boards->count() }} boards</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row" id="shared-boards">
                            @foreach($shared_boards as $project)
                                <div class="col-xl-3 col-md-6 mb-4">
                                    <div class="card board-card h-100" data-project-id="{{ $project->id }}" onclick="openBoard({{ $project->id }})">
                                        <div class="card-header board-header bg-gradient-{{ $loop->index % 4 == 0 ? 'info' : ($loop->index % 4 == 1 ? 'warning' : ($loop->index % 4 == 2 ? 'primary' : 'success')) }} p-3">
                                            <div class="board-pattern"></div>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="mb-2">{{ $project->name }}</h6>
                                            <p class="text-sm text-muted mb-3 flex-grow-1">{{ Str::limit($project->description, 80) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-share-alt text-info me-2"></i>
                                                    <small class="text-muted">Shared</small>
                                                </div>
                                                <i class="fas fa-arrow-right text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Create Board Modal --}}
    <div class="modal fade" id="createBoardModal" tabindex="-1" aria-labelledby="createBoardModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBoardModalLabel">Create New Board</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createBoardForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="boardName" class="form-control-label">Board Name *</label>
                            <input type="text" class="form-control" id="boardName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="boardDescription" class="form-control-label">Description</label>
                            <textarea class="form-control" id="boardDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="boardYear" class="form-control-label">Year</label>
                                    <select class="form-control" id="boardYear" name="year">
                                        @for($year = date('Y'); $year <= date('Y') + 5; $year++)
                                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="boardDepartment" class="form-control-label">Department</label>
                                    <select class="form-control" id="boardDepartment" name="department">
                                        <option value="general">General</option>
                                        <option value="gembong">GEMBONG</option>
                                        <option value="purchasing">Purchasing</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="hr">Human Resources</option>
                                        <option value="finance">Finance</option>
                                        <option value="it">IT</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Board</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
/* Board Cards */
.board-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.board-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.board-header {
    height: 80px;
    position: relative;
    overflow: hidden;
    border-radius: 0.75rem 0.75rem 0 0 !important;
}

.board-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.3;
    background-image: 
        radial-gradient(circle at 20% 50%, white 2px, transparent 2px),
        radial-gradient(circle at 80% 50%, white 2px, transparent 2px),
        radial-gradient(circle at 40% 20%, white 1px, transparent 1px),
        radial-gradient(circle at 60% 80%, white 1px, transparent 1px);
    background-size: 30px 30px, 30px 30px, 20px 20px, 20px 20px;
}

.create-board-card {
    border: 2px dashed #d1d5db;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.3s ease;
}

.create-board-card:hover {
    border-color: #6366f1;
    background: #f0f9ff;
    transform: translateY(-2px);
}

/* Search functionality */
.board-card.hidden {
    display: none !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .board-card {
        margin-bottom: 1rem;
    }
}

/* Animation for new boards */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.board-card.new-board {
    animation: slideInUp 0.5s ease-out;
}
</style>
@endpush

@push('js')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const allCards = document.querySelectorAll('.board-card');
    
    allCards.forEach(card => {
        const title = card.querySelector('h6');
        if (title) {
            const titleText = title.textContent.toLowerCase();
            if (titleText.includes(searchTerm) || searchTerm === '') {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        }
    });
});

// Open board function
function openBoard(projectId) {
    window.location.href = `{{ url('msbrd/show') }}/${projectId}`;
}

// Open create board modal
function openCreateBoardModal() {
    const modal = new bootstrap.Modal(document.getElementById('createBoardModal'));
    modal.show();
}

// Create board form submission
document.getElementById('createBoardForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ url("msbrd") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Board created successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('createBoardModal')).hide();
            
            // Add new board to the grid
            addNewBoardToGrid(data.board);
            
            // Reset form
            document.getElementById('createBoardForm').reset();
        } else {
            showAlert(data.message || 'Error creating board', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error creating board', 'danger');
    });
});

// Add new board to grid
function addNewBoardToGrid(board) {
    const myBoardsGrid = document.getElementById('my-boards');
    const createCard = myBoardsGrid.querySelector('.create-board-card').parentElement;
    
    const newBoardHtml = `
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card board-card h-100 new-board" data-project-id="${board.id}" onclick="openBoard(${board.id})">
                <div class="card-header board-header bg-gradient-primary p-3">
                    <div class="board-pattern"></div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="mb-2">${board.name}</h6>
                    <p class="text-sm text-muted mb-3 flex-grow-1">${board.description || ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-sm bg-gradient-success me-2">Active</span>
                            <small class="text-muted">0 tasks</small>
                        </div>
                        <i class="fas fa-arrow-right text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    createCard.insertAdjacentHTML('beforebegin', newBoardHtml);
}

// Show alert function
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
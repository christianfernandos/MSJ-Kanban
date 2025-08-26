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

    {{-- Create Board Modal - NEW DESIGN --}}
    <div class="modal fade" id="createBoardModal" tabindex="-1" aria-labelledby="createBoardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title fw-bold" id="createBoardModalLabel">Create Board</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createBoardForm">
                    <div class="modal-body px-4 py-4">
                        <!-- Background Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3 d-block">Background</label>
                            <div class="background-colors">
                                <div class="row g-3">
                                    <div class="col-auto">
                                        <div class="bg-color-option selected" style="background-color: #10b981;" data-color="emerald" title="Emerald"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #0891b2;" data-color="cyan" title="Cyan"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #eab308;" data-color="yellow" title="Yellow"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #1f2937;" data-color="gray" title="Gray"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #881337;" data-color="rose" title="Rose"></div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #dc2626;" data-color="red" title="Red"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #9333ea;" data-color="purple" title="Purple"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #06b6d4;" data-color="sky" title="Sky"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #65a30d;" data-color="lime" title="Lime"></div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="bg-color-option" style="background-color: #1e40af;" data-color="blue" title="Blue"></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedColor" name="background_color" value="emerald">
                        </div>

                        <!-- Member Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Member</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-1" id="addMemberBtn">
                                    Add
                                </button>
                            </div>
                            <div id="membersList" class="members-container">
                                <!-- Members will be added here -->
                            </div>
                        </div>

                        <!-- Board Title -->
                        <div class="mb-4">
                            <label for="boardName" class="form-label fw-bold mb-3 d-block">Board title</label>
                            <input type="text" class="form-control form-control-lg" id="boardName" name="name" 
                                   placeholder="Enter board title here..." required>
                        </div>

                        <!-- Hidden fields for compatibility -->
                        <input type="hidden" id="boardDescription" name="description" value="">
                        <input type="hidden" id="boardYear" name="year" value="{{ date('Y') }}">
                        <input type="hidden" id="boardDepartment" name="department" value="general">
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-6" style="background-color: #a8d5d1; border-color: #a8d5d1; color: #333;">
                            Create Board
                        </button>
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

/* NEW MODAL STYLES */
/* Modal Styles */
.modal-dialog.modal-lg {
    max-width: 600px;
    margin: 1.75rem auto;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    min-height: 500px;
}

.modal-header .modal-title {
    font-size: 1.5rem;
    color: #374151;
}

.modal-body {
    padding: 2rem 2.5rem;
}

/* Background Color Options */
.background-colors {
    max-width: 100%;
    margin-bottom: 1rem;
}

.bg-color-option {
    width: 50px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 3px solid transparent;
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    user-select: none;
    margin: 0 5px 10px 0;
}

.bg-color-option:hover {
    transform: scale(1.05) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    border-color: #374151;
}

.bg-color-option.selected {
    border-color: #374151 !important;
    transform: scale(1.1) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3) !important;
}

.bg-color-option.selected::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 18px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    z-index: 10;
    pointer-events: none;
}

.bg-color-option:active {
    transform: scale(0.95) !important;
}

/* Members Container */
.members-container {
    min-height: 40px;
    border: 1px dashed #d1d5db;
    border-radius: 8px;
    padding: 10px;
    background-color: #f9fafb;
}

/* Form Styles */
.form-label {
    color: #374151;
    font-size: 0.95rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    padding: 0.75rem 1rem;
}

.form-control:focus {
    border-color: #a8d5d1;
    box-shadow: 0 0 0 3px rgba(168, 213, 209, 0.1);
}

.form-control-lg {
    font-size: 1rem;
    padding: 1rem;
}

/* Button Styles */
.btn-outline-secondary {
    border-color: #d1d5db;
    color: #6b7280;
    font-size: 0.875rem;
}

.btn-outline-secondary:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
}

/* Members List */
#membersList {
    min-height: 20px;
}

.member-item {
    display: inline-block;
    background-color: #f3f4f6;
    padding: 0.25rem 0.75rem;
    margin: 0.25rem 0.25rem 0.25rem 0;
    border-radius: 20px;
    font-size: 0.875rem;
    color: #374151;
}

.member-item .remove-member {
    margin-left: 0.5rem;
    cursor: pointer;
    color: #9ca3af;
}

.member-item .remove-member:hover {
    color: #ef4444;
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

// Initialize modal features when DOM is ready and on modal show
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing modal features');
    
    // Also reinitialize when modal is shown
    const createBoardModal = document.getElementById('createBoardModal');
    if (createBoardModal) {
        createBoardModal.addEventListener('shown.bs.modal', function() {
            console.log('Modal shown - Reinitializing features');
            setTimeout(initializeModalFeatures, 200);
        });
        
        createBoardModal.addEventListener('show.bs.modal', function() {
            console.log('Modal about to show - Preparing features');
            setTimeout(initializeModalFeatures, 100);
        });
    }
    
    // Initial setup
    setTimeout(initializeModalFeatures, 500);
});

// Initialize modal features
function initializeModalFeatures() {
    console.log('=== Initializing modal features ===');
    
    // Background color selection functionality
    const colorOptions = document.querySelectorAll('.bg-color-option');
    const selectedColorInput = document.getElementById('selectedColor');
    
    console.log('Found color options:', colorOptions.length);
    console.log('Selected color input:', selectedColorInput);
    
    if (colorOptions.length > 0) {
        colorOptions.forEach((option, index) => {
            // Remove existing event listeners by cloning
            const newOption = option.cloneNode(true);
            option.parentNode.replaceChild(newOption, option);
            
            // Add fresh event listener
            newOption.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Color clicked:', this.dataset.color);
                
                // Remove selected class from all options
                document.querySelectorAll('.bg-color-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input
                if (selectedColorInput) {
                    selectedColorInput.value = this.dataset.color;
                    console.log('Color value updated to:', selectedColorInput.value);
                }
            });
            
            // Visual feedback
            newOption.addEventListener('mouseenter', function() {
                if (!this.classList.contains('selected')) {
                    this.style.transform = 'scale(1.05)';
                    this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.25)';
                }
            });
            
            newOption.addEventListener('mouseleave', function() {
                if (!this.classList.contains('selected')) {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = 'none';
                }
            });
            
            console.log(`Color option ${index + 1} initialized:`, newOption.dataset.color);
        });
        
        console.log('All color options initialized successfully');
    } else {
        console.error('No color options found!');
    }
    
    // Member management functionality
    const addMemberBtn = document.getElementById('addMemberBtn');
    const membersList = document.getElementById('membersList');
    let memberCount = 0;
    
    if (addMemberBtn) {
        // Remove existing event listener
        const newAddMemberBtn = addMemberBtn.cloneNode(true);
        addMemberBtn.parentNode.replaceChild(newAddMemberBtn, addMemberBtn);
        
        newAddMemberBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const memberEmail = prompt('Enter member email:');
            if (memberEmail && memberEmail.trim()) {
                addMember(memberEmail.trim());
            }
        });
        
        console.log('Add member button initialized');
    }
    
    function addMember(email) {
        memberCount++;
        const memberItem = document.createElement('div');
        memberItem.className = 'member-item';
        memberItem.innerHTML = `
            ${email}
            <span class="remove-member" onclick="removeMember(this)">&times;</span>
            <input type="hidden" name="members[]" value="${email}">
        `;
        if (membersList) {
            membersList.appendChild(memberItem);
        }
    }
    
    window.removeMember = function(element) {
        element.parentElement.remove();
        memberCount--;
    };
    
    console.log('=== Modal features initialization complete ===');
}

// Create board form submission
document.addEventListener('DOMContentLoaded', function() {
    const createBoardForm = document.getElementById('createBoardForm');
    
    if (createBoardForm) {
        createBoardForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submitted');
            
            // Get form data
            const formData = new FormData(this);
            
            // Add selected background color to form data
            const selectedColorInput = document.getElementById('selectedColor');
            const selectedColor = selectedColorInput ? selectedColorInput.value : 'emerald';
            formData.set('background_color', selectedColor);
            
            // Get board name
            const boardName = document.getElementById('boardName').value;
            if (!boardName || boardName.trim() === '') {
                showAlert('Please enter a board title', 'danger');
                return;
            }
            
            // Set default values for required fields
            formData.set('name', boardName.trim());
            formData.set('description', formData.get('description') || '');
            formData.set('year', formData.get('year') || '{{ date("Y") }}');
            formData.set('department', formData.get('department') || 'general');
            
            // Add members if any
            const memberInputs = document.querySelectorAll('#membersList input[name="members[]"]');
            memberInputs.forEach((input, index) => {
                formData.append(`members[${index}]`, input.value);
            });
            
            console.log('Submitting form with data:');
            console.log('- Board name:', boardName);
            console.log('- Selected color:', selectedColor);
            console.log('- Description:', formData.get('description'));
            console.log('- Year:', formData.get('year'));
            console.log('- Department:', formData.get('department'));
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Creating...';
            submitBtn.disabled = true;
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                showAlert('Security token missing. Please refresh the page.', 'danger');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
            
            // Debug: Log all form data
            console.log('=== Form Data Debug ===');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            console.log('======================');
            
            fetch('{{ url("msbrd") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    // Try to get error details from response
                    return response.text().then(text => {
                        console.error('Response text:', text);
                        throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 200)}`);
                    });
                }
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    showAlert('Board created successfully!', 'success');
                    
                    // Close modal
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('createBoardModal'));
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    
                    // Add new board to the grid
                    if (data.board) {
                        addNewBoardToGrid(data.board);
                    }
                    
                    // Reset form
                    createBoardForm.reset();
                    resetModalForm();
                    
                    // Reload page to show new board
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                } else {
                    showAlert(data.message || 'Error creating board', 'danger');
                    console.error('Server error:', data);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showAlert('Error creating board: ' + error.message, 'danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});

// Reset modal form
function resetModalForm() {
    // Reset color selection to default (emerald)
    document.querySelectorAll('.bg-color-option').forEach(opt => opt.classList.remove('selected'));
    const emeraldOption = document.querySelector('.bg-color-option[data-color="emerald"]');
    if (emeraldOption) {
        emeraldOption.classList.add('selected');
    }
    document.getElementById('selectedColor').value = 'emerald';
    
    // Clear members list
    document.getElementById('membersList').innerHTML = '';
    
    // Reinitialize modal features for fresh event listeners
    setTimeout(initializeModalFeatures, 100);
}

// Add new board to grid
function addNewBoardToGrid(board) {
    console.log('Adding new board to grid:', board);
    
    const myBoardsGrid = document.getElementById('my-boards');
    if (!myBoardsGrid) {
        console.error('My boards grid not found');
        return;
    }
    
    const createCard = myBoardsGrid.querySelector('.create-board-card');
    if (!createCard) {
        console.error('Create board card not found');
        return;
    }
    
    const createCardContainer = createCard.closest('.col-xl-3');
    if (!createCardContainer) {
        console.error('Create card container not found');
        return;
    }
    
    // Color mapping for gradient classes
    const colorMapping = {
        'emerald': 'success',
        'cyan': 'info',
        'yellow': 'warning',
        'gray': 'secondary',
        'rose': 'danger',
        'red': 'danger',
        'purple': 'primary',
        'sky': 'info',
        'lime': 'success',
        'blue': 'primary'
    };
    
    const gradientClass = colorMapping[board.background_color] || 'primary';
    
    const newBoardHtml = `
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card board-card h-100 new-board" data-project-id="${board.id}" onclick="openBoard(${board.id})">
                <div class="card-header board-header bg-gradient-${gradientClass} p-3">
                    <div class="board-pattern"></div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="mb-2">${board.name}</h6>
                    <p class="text-sm text-muted mb-3 flex-grow-1">${board.description || 'No description'}</p>
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
    
    createCardContainer.insertAdjacentHTML('beforebegin', newBoardHtml);
    
    // Update board count
    const boardCountBadge = document.querySelector('#my-boards').closest('.card').querySelector('.badge');
    if (boardCountBadge) {
        const currentCount = parseInt(boardCountBadge.textContent) || 0;
        boardCountBadge.textContent = `${currentCount + 1} boards`;
    }
    
    console.log('New board added to grid successfully');
}

// Show alert function
function showAlert(message, type) {
    console.log('Showing alert:', type, message);
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
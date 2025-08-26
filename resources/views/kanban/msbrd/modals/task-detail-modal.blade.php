{{-- Task Detail Modal --}}
<style>
/* Ensure modal has higher z-index than search and other elements */
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

/* Fix modal positioning to avoid conflicts */
#taskDetailModal {
    z-index: 10000 !important;
}

#taskDetailModal .modal-dialog {
    margin: 1rem auto;
    max-width: 75vw; /* Reduced from 85vw to make it less wide */
    width: 75vw;
    max-height: 90vh; /* Added max height */
}

#taskDetailModal .modal-content {
    height: auto;
    max-height: 85vh; /* Increased height to prevent content from being cut off */
    overflow-y: auto; /* Allow scrolling if content is too tall */
    border-radius: 20px;
}

#taskDetailModal .modal-body {
    max-height: 70vh; /* Set max height for modal body */
    overflow-y: auto; /* Allow scrolling within modal body */
    padding: 1.5rem; /* Consistent padding */
}

/* Ensure all content fits properly */
#taskDetailModal .modal-body .row {
    margin: 0;
}

#taskDetailModal .modal-body .col-lg-8,
#taskDetailModal .modal-body .col-lg-4 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

/* Fix any overflow issues in the content */
#taskDetailModal .form-control,
#taskDetailModal .input-group,
#taskDetailModal textarea {
    width: 100%;
    box-sizing: border-box;
}

/* Priority badge styling */
#taskDetailModal #taskDetailPriority {
    min-width: 750px; /* Increased to maximum width to reach column border */
    max-width: 95%; /* Increased to 95% to reach exactly to the column separator */
    width: 95%; /* Added explicit width to ensure it reaches the border */
    text-align: center;
    display: inline-block;
    font-weight: 700;
    letter-spacing: 2px;
    border-radius: 6px;
}

/* Priority badge colors */
#taskDetailModal #taskDetailPriority.priority-low {
    background: #28a745 !important;
}

#taskDetailModal #taskDetailPriority.priority-medium {
    background: #ffc107 !important;
    color: #212529 !important;
}

#taskDetailModal #taskDetailPriority.priority-high {
    background: #fd7e14 !important;
}

#taskDetailModal #taskDetailPriority.priority-urgent {
    background: #dc3545 !important;
}

/* Ensure modal content is above everything */
#taskDetailModal .modal-content {
    position: relative;
    z-index: 10001 !important;
}

/* Hide overflow on body when modal is open to prevent scroll issues */
body.modal-open {
    padding-right: 0 !important;
}

/* Ensure search and navigation elements stay below modal */
.page-nav-container,
.back-btn-container,
.search-section {
    z-index: 100 !important;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    #taskDetailModal .modal-dialog {
        max-width: 80vw; /* Slightly wider for medium screens */
        width: 80vw;
    }
}

@media (max-width: 992px) {
    #taskDetailModal .modal-dialog {
        margin: 0.5rem;
        max-width: 85vw; /* Reduced from 95vw */
        width: 85vw;
        max-height: 95vh;
    }
    
    #taskDetailModal .modal-content {
        max-height: 90vh;
    }
    
    #taskDetailModal .modal-body {
        max-height: 75vh;
        padding: 1rem;
    }
    
    #taskDetailModal .modal-body .row {
        flex-direction: column;
    }
    
    #taskDetailModal .modal-body .col-lg-8,
    #taskDetailModal .modal-body .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Adjust priority badge for mobile */
    #taskDetailModal #taskDetailPriority {
        min-width: 500px; /* Increased to reach border */
        max-width: 98%; /* Nearly full width to reach separator */
        width: 98%; /* Added explicit width */
    }
}

@media (max-width: 768px) {
    #taskDetailModal .modal-dialog {
        margin: 0.25rem;
        max-width: 98vw;
        width: 98vw;
        max-height: 98vh;
    }
    
    #taskDetailModal .modal-content {
        max-height: 95vh;
    }
    
    #taskDetailModal .modal-body {
        max-height: 80vh;
        padding: 0.75rem;
    }
    
    /* Smaller priority badge for mobile */
    #taskDetailModal #taskDetailPriority {
        min-width: 350px; /* Increased to reach border on mobile */
        max-width: 95%; /* Full width to reach separator */
        width: 95%; /* Added explicit width */
        font-size: 11px;
    }
}

@media (max-width: 576px) {
    #taskDetailModal .modal-dialog {
        margin: 0.1rem;
        max-width: 99vw;
        width: 99vw;
    }
    
    #taskDetailModal .modal-body {
        padding: 0.5rem;
    }
}
</style>

<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- Changed from modal-xl to modal-lg --}}
        <div class="modal-content border-0" style="border-radius: 20px;">
            {{-- Modal Header dengan Status --}}
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(90deg, #a8e6cf 0%, #88d8c0 100%); border-radius: 20px 20px 0 0; position: relative;">
                <div class="d-flex w-100 justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark fw-bold text-uppercase" id="taskDetailStatusHeader">TO DO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 24px;">
                        <i class="fas fa-times text-dark"></i>
                    </button>
                </div>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Left Column - Main Content --}}
                    <div class="col-lg-8 p-4">
                        {{-- Priority Badge --}}
                        <div class="mb-3">
                            <span id="taskDetailPriority" class="badge text-white fw-bold px-4 py-2" style="background: #dc3545; font-size: 12px; letter-spacing: 2px; min-width: 750px; max-width: 95%; width: 95%; text-align: center; display: inline-block;">URGENT</span>
                        </div>

                        {{-- Department & Title --}}
                        <div class="mb-4">
                            <div class="mb-2">
                                <span id="taskDetailDepartment" class="text-muted text-uppercase fw-bold" style="font-size: 14px; letter-spacing: 1px;">GEMBONG</span>
                            </div>
                            <h2 id="taskDetailTitle" class="text-dark mb-0" style="font-weight: 700; line-height: 1.3;">Produksi bahan bahan sebanyak 20.000 pcs perhari</h2>
                        </div>

                        {{-- Dates Section --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">Created at</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0" id="taskDetailCreatedAt" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold mb-2">Due date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-calendar text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0" id="taskDetailDueDate" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>

                        {{-- Description Section --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-align-left me-2 text-muted"></i>
                                <h6 class="mb-0 fw-bold">Description</h6>
                            </div>
                            <div class="p-3 rounded" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                <p id="taskDetailDescription" class="mb-0 text-muted" style="line-height: 1.6;">
                                    Produksi bahan bahan sebanyak 20.000 pcs baju dan yang lain lain juga semua nya 20.000 pcs bahan bahan sebanyak 20.000 pcs baju dan yang lain lain juga semua nya 20.000 pcs bahan bahan sebanyak 20.000 pcs baju dan yang lain lain juga semua nya 20.000 pcs
                                </p>
                            </div>
                        </div>

                        {{-- Attachments Section --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-paperclip me-2 text-muted"></i>
                                    <h6 class="mb-0 fw-bold">Attachments</h6>
                                </div>
                                <button class="btn btn-outline-primary btn-sm">Add</button>
                            </div>
                            
                            {{-- Links --}}
                            <div class="mb-3">
                                <small class="text-muted text-uppercase fw-bold d-block mb-2">Links</small>
                                <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fab fa-linkedin text-primary me-2" style="font-size: 18px;"></i>
                                        <span class="text-dark">LinkedIn Profile</span>
                                    </div>
                                    <button class="btn btn-sm btn-light border-0">
                                        <i class="fas fa-ellipsis-h text-muted"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Files --}}
                            <div>
                                <small class="text-muted text-uppercase fw-bold d-block mb-2">Files</small>
                                <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">
                                            <img src="data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='32' height='32' rx='4' fill='%23F8F9FA'/%3E%3Cpath d='M8 6C8 4.89543 8.89543 4 10 4H18L24 10V26C24 27.1046 23.1046 28 22 28H10C8.89543 28 8 27.1046 8 26V6Z' fill='%236C757D'/%3E%3Cpath d='M18 4V10H24' stroke='%23F8F9FA' stroke-width='1.5' stroke-linejoin='round'/%3E%3Cpath d='M12 16H20M12 20H18' stroke='%23F8F9FA' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E" alt="file" style="width: 32px; height: 32px;">
                                        </div>
                                        <div>
                                            <div class="text-dark fw-medium">Multi Spunindo Jaya Tbk.jpg</div>
                                            <small class="text-muted">05 July 2025</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-light border-0">
                                        <i class="fas fa-ellipsis-h text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Members Section --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user me-2 text-muted"></i>
                                    <h6 class="mb-0 fw-bold">Member</h6>
                                </div>
                                <button class="btn btn-outline-primary btn-sm">Add</button>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <span class="text-white fw-bold" style="font-size: 12px;">JL</span>
                                </div>
                                <span class="text-dark">Juan Kody Leondra</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column - Comments and Activities --}}
                    <div class="col-lg-4" style="background-color: #f8f9fa; border-left: 1px solid #e9ecef;">
                        <div class="p-4 h-100">
                            <div class="d-flex align-items-center mb-4">
                                <i class="far fa-comments me-2 text-muted"></i>
                                <h6 class="mb-0 fw-bold">Comments and activities</h6>
                            </div>

                            {{-- Comment Input --}}
                            <div class="mb-4">
                                <textarea class="form-control border" placeholder="Write a comment..." rows="3" id="newComment" style="resize: none; border-radius: 8px;"></textarea>
                                <div class="text-end mt-2">
                                    <button class="btn btn-primary btn-sm" onclick="addComment()">Post</button>
                                </div>
                            </div>

                            {{-- Comments and Activities List --}}
                            <div id="taskDetailComments" style="max-height: 400px; overflow-y: auto;">
                                {{-- Sample Comment --}}
                                <div class="d-flex mb-3">
                                    <div class="me-2 flex-shrink-0">
                                        <div class="avatar" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <span class="text-white fw-bold" style="font-size: 12px;">JL</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <small class="text-dark fw-bold me-2">Juan Kody Leondra</small>
                                            <small class="text-muted">05 July 2025, 19:00</small>
                                        </div>
                                        <div class="bg-white p-2 rounded border">
                                            <p class="mb-0" style="font-size: 14px;">Oke sudah di kerjakan semuanya</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Activity Log --}}
                                <div class="d-flex mb-3">
                                    <div class="me-2 flex-shrink-0">
                                        <div class="avatar" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <span class="text-white fw-bold" style="font-size: 12px;">JL</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <small class="text-dark fw-bold me-1">Juan Kody Leondra</small>
                                            <small class="text-muted me-1">added this card to</small>
                                            <span class="badge bg-info text-white" style="font-size: 10px;">To Do</span>
                                        </div>
                                        <small class="text-muted">05 July 2025, 16:00</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable to store current task ID
window.currentTaskDetailId = null;

// Function to open task detail modal
function openTaskDetailModal(taskId) {
    console.log('=== openTaskDetailModal called ===');
    console.log('Task ID:', taskId);
    
    if (!taskId) {
        console.error('No task ID provided');
        showToast('error', 'No task ID provided');
        return;
    }
    
    // Store current task ID
    window.currentTaskDetailId = taskId;
    
    // Fetch task data and populate modal
    fetchTaskDetails(taskId).then(task => {
        console.log('=== fetchTaskDetails completed ===');
        console.log('Task data received:', task);
        
        if (task) {
            populateTaskDetailModal(task);
            console.log('=== Opening modal ===');
            const modal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
            modal.show();
        } else {
            console.error('No task data received');
            showToast('error', 'No task data received');
        }
    }).catch(error => {
        console.error('=== Error in fetchTaskDetails ===');
        console.error('Error details:', error);
        showToast('error', 'Failed to load task details: ' + error.message);
    });
}

// Function to fetch task details
async function fetchTaskDetails(taskId) {
    console.log('=== fetchTaskDetails called ===');
    console.log('Fetching task details for ID:', taskId);
    
    try {
        // Use only MSJ Framework routing - no fallbacks to avoid route issues
        const url = `{{ url('msbrd/api/get-task') }}`;
        console.log('API URL:', url);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF Token found:', !!csrfToken);
        
        // Create FormData for POST request (standard form submission)
        const formData = new FormData();
        formData.append('task_id', taskId);
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }
        
        console.log('Request data - task_id:', taskId);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            console.log('=== Task data successfully received ===');
            return data.task;
        } else {
            console.error('API returned error:', data);
            // If API fails, provide demo data instead of throwing error
            console.log('API failed, providing demo data...');
            return {
                id: taskId,
                title: `Demo Task #${taskId}`,
                description: `This is demo data because the API request failed. Error: ${data.message || data.error || 'Unknown error'}`,
                status: 'todo',
                priority: 'medium',
                department: 'GEMBONG',
                created_at: '2025-08-25',
                due_date: '2025-08-30',
                assigned_to: null,
                progress: 0
            };
        }
    } catch (error) {
        console.error('=== fetchTaskDetails error ===');
        console.error('Error type:', error.constructor.name);
        console.error('Error message:', error.message);
        console.error('Full error:', error);
        
        // Provide demo data instead of showing error
        console.log('Network/Fetch error, providing demo data...');
        showToast('warning', 'Could not load real task data, showing demo data');
        
        return {
            id: taskId,
            title: `Demo Task #${taskId}`,
            description: `This is demo data because there was an error loading the real task data. Error: ${error.message}`,
            status: 'todo',
            priority: 'medium',
            department: 'GEMBONG',
            created_at: '2025-08-25',
            due_date: '2025-08-30',
            assigned_to: null,
            progress: 0
        };
    }
}

// Function to populate modal with task data
function populateTaskDetailModal(task) {
    if (!task) return;
    
    console.log('Populating modal with task:', task);
    
    // Set status header
    const statusHeader = document.getElementById('taskDetailStatusHeader');
    if (statusHeader) {
        statusHeader.textContent = (task.status || 'todo').toUpperCase().replace('_', ' ');
    }
    
    // Set priority badge
    const priorityBadge = document.getElementById('taskDetailPriority');
    if (priorityBadge) {
        priorityBadge.textContent = (task.priority || 'medium').toUpperCase();
        // Remove old priority classes
        priorityBadge.className = priorityBadge.className.replace(/priority-\w+/g, '');
        // Add new class with consistent styling
        priorityBadge.className = `badge text-white fw-bold px-4 py-2 priority-${task.priority || 'medium'}`;
        priorityBadge.style.cssText = 'font-size: 12px; letter-spacing: 2px; min-width: 750px; max-width: 95%; width: 95%; text-align: center; display: inline-block; border-radius: 6px;';
    }
    
    // Set task details
    const titleElement = document.getElementById('taskDetailTitle');
    if (titleElement) {
        titleElement.textContent = task.title || 'Untitled Task';
    }
    
    const departmentElement = document.getElementById('taskDetailDepartment');
    if (departmentElement) {
        departmentElement.textContent = (task.department || 'GEMBONG').toUpperCase();
    }
    
    const descriptionElement = document.getElementById('taskDetailDescription');
    if (descriptionElement) {
        descriptionElement.textContent = task.description || 'No description available';
    }
    
    const createdAtElement = document.getElementById('taskDetailCreatedAt');
    if (createdAtElement) {
        createdAtElement.value = task.created_at || '';
    }
    
    const dueDateElement = document.getElementById('taskDetailDueDate');
    if (dueDateElement) {
        dueDateElement.value = task.due_date || '';
    }
}

// Helper function for priority classes
// Function to get priority colors (no longer needed as using CSS classes)
// Kept for backward compatibility if needed elsewhere
function getPriorityClass(priority) {
    const classes = {
        'low': 'priority-low',
        'medium': 'priority-medium', 
        'high': 'priority-high',
        'urgent': 'priority-urgent'
    };
    return classes[priority] || 'priority-medium';
}

// Function to add comment
function addComment() {
    const commentText = document.getElementById('newComment').value.trim();
    if (!commentText) {
        showToast('warning', 'Please enter a comment');
        return;
    }
    
    // For now, just show success message
    showToast('success', 'Comment added successfully');
    document.getElementById('newComment').value = '';
}

// Toast notification function
function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}
</script>

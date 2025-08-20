{{-- resources/views/kanban/modals/task-modal.blade.php --}}
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taskForm" method="POST" action="{{ url('kanban/tasks') }}">
                @csrf
                <input type="hidden" id="task_id" name="task_id" value="">
                <input type="hidden" id="_method" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="row">
                        {{-- Task Title --}}
                        <div class="col-12">
                            <div class="form-group">
                                <label for="task_title" class="form-control-label">Task Title *</label>
                                <input type="text" class="form-control" id="task_title" name="title" placeholder="Enter task title" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        {{-- Task Description --}}
                        <div class="col-12">
                            <div class="form-group">
                                <label for="task_description" class="form-control-label">Description</label>
                                <textarea class="form-control" id="task_description" name="description" rows="3" placeholder="Enter task description"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- Department --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_department" class="form-control-label">Department *</label>
                                <select class="form-control" id="task_department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="gembong">GEMBONG</option>
                                    <option value="production">PRODUCTION</option>
                                    <option value="quality">QUALITY</option>
                                    <option value="purchasing">PURCHASING</option>
                                    <option value="logistics">LOGISTICS</option>
                                    <option value="hr">HUMAN RESOURCES</option>
                                    <option value="finance">FINANCE</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        {{-- Priority --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_priority" class="form-control-label">Priority *</label>
                                <select class="form-control" id="task_priority" name="priority" required>
                                    <option value="low">🟢 Low</option>
                                    <option value="medium" selected>🟡 Medium</option>
                                    <option value="high">🔴 High</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- Status --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="task_status" class="form-control-label">Status *</label>
                                <select class="form-control" id="task_status" name="status" required>
                                    <option value="todo">📋 To Do</option>
                                    <option value="progress">🔄 In Progress</option>
                                    <option value="done">✅ Done</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        {{-- Start Date --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="task_start_date" class="form-control-label">Start Date</label>
                                <input type="date" class="form-control" id="task_start_date" name="start_date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        {{-- Due Date --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="task_due_date" class="form-control-label">Due Date</label>
                                <input type="date" class="form-control" id="task_due_date" name="due_date">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        {{-- Assigned Users --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_assignees" class="form-control-label">Assign To</label>
                                <select class="form-control" id="task_assignees" name="assignees[]" multiple>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple users</small>
                            </div>
                        </div>
                        
                        {{-- Tags --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_tags" class="form-control-label">Tags</label>
                                <input type="text" class="form-control" id="task_tags" name="tags" placeholder="Enter tags separated by commas">
                                <small class="form-text text-muted">Example: urgent, meeting, review</small>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Estimated Hours --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_estimated_hours" class="form-control-label">Estimated Hours</label>
                                <input type="number" class="form-control" id="task_estimated_hours" name="estimated_hours" min="0" step="0.5" placeholder="0">
                                <small class="form-text text-muted">Estimated time to complete this task</small>
                            </div>
                        </div>
                        
                        {{-- Progress --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="task_progress" class="form-control-label">Progress (%)</label>
                                <input type="range" class="form-range" id="task_progress" name="progress" min="0" max="100" value="0" oninput="updateProgressValue(this.value)">
                                <div class="d-flex justify-content-between">
                                    <small>0%</small>
                                    <small id="progress-value">0%</small>
                                    <small>100%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Checklist --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-control-label">Checklist Items</label>
                                <div id="checklist-container">
                                    <!-- Checklist items will be added here -->
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addChecklistItem()">
                                    <i class="fas fa-plus me-1"></i> Add Checklist Item
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- File Attachments --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="task_attachments" class="form-control-label">Attachments</label>
                                <input type="file" class="form-control" id="task_attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                                <small class="form-text text-muted">Allowed formats: JPG, PNG, PDF, DOC, XLS (Max 10MB per file)</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> <span id="submit-text">Save Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Task form handling
document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const taskId = document.getElementById('task_id').value;
    const method = taskId ? 'PUT' : 'POST';
    const url = taskId ? `{{ url('kanban/tasks') }}/${taskId}` : '{{ url('kanban/tasks') }}';
    
    // Set method for Laravel
    if (taskId) {
        document.getElementById('_method').value = 'PUT';
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
    submitBtn.disabled = true;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message || 'Task saved successfully');
            $('#taskModal').modal('hide');
            location.reload(); // Refresh to show updated data
        } else {
            // Handle validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const input = document.getElementById(`task_${key}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentNode.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = data.errors[key][0];
                        }
                    }
                });
            }
            toastr.error(data.message || 'Failed to save task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while saving the task');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Reset form when modal is hidden
$('#taskModal').on('hidden.bs.modal', function() {
    const form = document.getElementById('taskForm');
    form.reset();
    form.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
    document.getElementById('task_id').value = '';
    document.getElementById('_method').value = 'POST';
    document.getElementById('taskModalLabel').textContent = 'Add New Task';
    document.getElementById('submit-text').textContent = 'Save Task';
    document.getElementById('progress-value').textContent = '0%';
    document.getElementById('checklist-container').innerHTML = '';
});

// Update progress value display
function updateProgressValue(value) {
    document.getElementById('progress-value').textContent = value + '%';
}

// Checklist functionality
let checklistCounter = 0;

function addChecklistItem() {
    checklistCounter++;
    const container = document.getElementById('checklist-container');
    const itemHtml = `
        <div class="input-group mb-2" id="checklist-item-${checklistCounter}">
            <input type="text" class="form-control" name="checklist_items[]" placeholder="Enter checklist item">
            <button type="button" class="btn btn-outline-danger" onclick="removeChecklistItem(${checklistCounter})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function removeChecklistItem(itemId) {
    document.getElementById(`checklist-item-${itemId}`).remove();
}

// Auto-set due date when start date is selected
document.getElementById('task_start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const dueDateInput = document.getElementById('task_due_date');
    
    if (!dueDateInput.value && startDate) {
        // Set due date to 7 days after start date by default
        const dueDate = new Date(startDate);
        dueDate.setDate(dueDate.getDate() + 7);
        dueDateInput.value = dueDate.toISOString().split('T')[0];
    }
});

// Initialize select2 for better multi-select experience (if available)
if (typeof $.fn.select2 !== 'undefined') {
    $('#task_assignees').select2({
        placeholder: 'Select users to assign',
        allowClear: true,
        dropdownParent: $('#taskModal')
    });
}
</script>
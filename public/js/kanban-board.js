/**
 * Kanban Board JavaScript
 * Handles drag and drop, task management, and board interactions
 */

let draggedTask = null;
let draggedFromColumn = null;

// Initialize Kanban Board
function initializeKanbanBoard() {
    initializeDragAndDrop();
    loadBoardData();
}

// Initialize drag and drop functionality
function initializeDragAndDrop() {
    // Add event listeners to all task cards
    document.querySelectorAll('.card[data-task-id]').forEach(card => {
        addTaskCardListeners(card);
    });

    // Add event listeners to all kanban columns
    document.querySelectorAll('.kanban-tasks').forEach(column => {
        addColumnListeners(column);
    });
}

// Add event listeners to a task card
function addTaskCardListeners(card) {
    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', handleDragEnd);
    card.addEventListener('click', handleCardClick);
}

// Add event listeners to a column
function addColumnListeners(column) {
    column.addEventListener('dragover', handleDragOver);
    column.addEventListener('drop', handleDrop);
    column.addEventListener('dragenter', handleDragEnter);
    column.addEventListener('dragleave', handleDragLeave);
}

// Drag start handler
function handleDragStart(e) {
    draggedTask = this;
    draggedFromColumn = this.closest('.kanban-tasks').dataset.status;
    
    this.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.outerHTML);
}

// Drag end handler
function handleDragEnd(e) {
    this.classList.remove('dragging');
    
    // Remove drag-over class from all columns
    document.querySelectorAll('.kanban-column').forEach(col => {
        col.classList.remove('drag-over');
    });
    
    draggedTask = null;
    draggedFromColumn = null;
}

// Drag over handler
function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    return false;
}

// Drag enter handler
function handleDragEnter(e) {
    this.closest('.kanban-column').classList.add('drag-over');
}

// Drag leave handler
function handleDragLeave(e) {
    // Only remove drag-over if we're leaving the column entirely
    if (!this.contains(e.relatedTarget)) {
        this.closest('.kanban-column').classList.remove('drag-over');
    }
}

// Drop handler
function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    const targetColumn = this;
    const targetStatus = targetColumn.dataset.status;
    
    if (draggedTask && draggedFromColumn !== targetStatus) {
        const taskId = draggedTask.dataset.taskId;
        
        // Move task in DOM immediately for better UX
        targetColumn.appendChild(draggedTask);
        
        // Update task counts
        updateTaskCounts();
        
        // Send API request to update task status
        moveTaskToColumn(taskId, targetStatus);
    }
    
    targetColumn.closest('.kanban-column').classList.remove('drag-over');
    return false;
}

// Move task to column via API
function moveTaskToColumn(taskId, newStatus) {
    fetch('/api/kanban/tasks/move', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            task_id: taskId,
            new_column: newStatus,
            new_position: 0 // For now, always add to top
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Task moved successfully!');
        } else {
            // Revert the move if API call failed
            showToast('error', data.error || 'Failed to move task');
            location.reload(); // Simple revert by reloading
        }
    })
    .catch(error => {
        console.error('Error moving task:', error);
        showToast('error', 'An error occurred while moving the task');
        location.reload(); // Simple revert by reloading
    });
}

// Load board data from API
function loadBoardData() {
    // This function can be used to refresh board data without page reload
    // For now, we'll use server-side rendering
}

// Initialize search functionality
function initializeSearch() {
    const searchInput = document.getElementById('taskSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            filterTasks(searchTerm);
        });
    }
}

// Filter tasks based on search term
function filterTasks(searchTerm) {
    document.querySelectorAll('.task-card').forEach(card => {
        const title = card.querySelector('.task-title').textContent.toLowerCase();
        const description = card.querySelector('.task-description');
        const descText = description ? description.textContent.toLowerCase() : '';
        
        if (title.includes(searchTerm) || descText.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize filters
function initializeFilters() {
    const assigneeFilter = document.getElementById('filterAssignee');
    const categoryFilter = document.getElementById('filterCategory');
    
    if (assigneeFilter) {
        assigneeFilter.addEventListener('change', applyFilters);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
}

// Apply filters
function applyFilters() {
    const assigneeFilter = document.getElementById('filterAssignee').value;
    const categoryFilter = document.getElementById('filterCategory').value;
    
    document.querySelectorAll('.task-card').forEach(card => {
        let showCard = true;
        
        // Check assignee filter
        if (assigneeFilter) {
            const assigneeImg = card.querySelector('.task-assignee img');
            if (!assigneeImg || !assigneeImg.alt.includes(assigneeFilter)) {
                showCard = false;
            }
        }
        
        // Check category filter
        if (categoryFilter && showCard) {
            const categoryBadge = card.querySelector('.badge');
            if (!categoryBadge || !categoryBadge.textContent.includes(categoryFilter)) {
                showCard = false;
            }
        }
        
        card.style.display = showCard ? 'block' : 'none';
    });
}

// Open task details (placeholder)
function openTaskDetails(taskId) {
    // Open task detail modal
    if (typeof openTaskDetailModal === 'function') {
        openTaskDetailModal(taskId);
    } else {
        console.error('openTaskDetailModal function not found');
        showToast('error', 'Task detail modal not available');
    }
}

// Handle card click to open task details
function handleCardClick(e) {
    // Don't open modal if dragging
    if (this.classList.contains('dragging')) {
        return;
    }
    
    const taskId = this.dataset.taskId;
    if (taskId) {
        openTaskDetails(taskId);
    } else {
        console.error('Task ID not found on card');
        showToast('error', 'Task ID not found');
    }
}

// Utility function to show toast notifications
function showToast(type, message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
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

// Update task counts in column headers
function updateTaskCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const status = column.dataset.status;
        const visibleTasks = column.querySelectorAll('.task-card:not([style*="display: none"])').length;
        const countBadge = column.querySelector('.task-count');
        if (countBadge) {
            countBadge.textContent = visibleTasks;
        }
    });
}

// Refresh board data
function refreshBoard() {
    location.reload(); // Simple refresh for now
}

// Auto-refresh board every 30 seconds (optional)
// setInterval(refreshBoard, 30000);

// Handle window resize for responsive design
window.addEventListener('resize', function() {
    // Adjust board layout if needed
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N to add new task
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        openAddTaskModal();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            bootstrap.Modal.getInstance(modal)?.hide();
        });
    }
});

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Kanban board initialized');
});

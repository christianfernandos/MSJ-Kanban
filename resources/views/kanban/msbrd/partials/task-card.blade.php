{{-- Task Card Component --}}
<div class="task-card priority-{{ $task->priority ?? 'medium' }}" data-task-id="{{ $task->id }}">
    <div class="task-labels">
        <span class="task-label priority-{{ $task->priority ?? 'medium' }}">{{ strtoupper($task->priority ?? 'MEDIUM') }}</span>
        @if($task->category)
            <span class="task-label">{{ $task->category->name }}</span>
        @endif
    </div>
    
    <div class="task-title">{{ $task->title }}</div>
    
    @if($task->description)
        <div class="task-description">{{ $task->description }}</div>
    @endif
    
    @if($task->progress !== null)
        <div class="task-progress">
            <div class="progress">
                <div class="progress-bar 
                    @if($task->progress < 25) bg-danger
                    @elseif($task->progress < 50) bg-warning
                    @elseif($task->progress < 75) bg-info
                    @else bg-success
                    @endif" 
                    style="width: {{ $task->progress }}%"></div>
            </div>
            <div class="task-progress-text">{{ $task->progress }}% Complete</div>
        </div>
    @endif
    
    <div class="task-meta">
        <div class="task-assignee">
            @if($task->assignee)
                <div class="task-assignee-avatar">
                    {{ strtoupper(substr($task->assignee->firstname ?? $task->assignee->username, 0, 1)) }}{{ strtoupper(substr($task->assignee->lastname ?? '', 0, 1)) }}
                </div>
                <span>{{ trim($task->assignee->firstname . ' ' . $task->assignee->lastname) ?: $task->assignee->username }}</span>
            @else
                <div class="task-assignee-avatar">?</div>
                <span>Unassigned</span>
            @endif
        </div>
        
        <div class="task-info">
            @if($task->due_date)
                @php
                    $dueDate = \Carbon\Carbon::parse($task->due_date);
                    $now = \Carbon\Carbon::now();
                    $isOverdue = $dueDate->isPast();
                    $isDueSoon = $dueDate->diffInDays($now) <= 3 && !$isOverdue;
                @endphp
                <div class="task-due-date {{ $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : '') }}">
                    <i class="fas fa-clock"></i> 
                    {{ $dueDate->format('M d') }}
                </div>
            @endif
            
            @if($task->status === 'done')
                <div class="task-due-date">
                    <i class="fas fa-check"></i> Completed
                </div>
            @endif
        </div>
    </div>
</div>
@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-tasks"></i> Tasks
                @if($selectedProject)
                    @php
                        $project = $projects->find($selectedProject);
                    @endphp
                    <small class="text-muted">- {{ $project->name }}</small>
                @endif
            </h2>
            <div>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="fas fa-plus"></i> Add Task
                </button>
            </div>
        </div>

        <!-- Project Filter -->
        <div class="mb-3">
            <form method="GET" action="{{ route('tasks.index') }}" class="d-flex align-items-center gap-2">
                <label for="project" class="form-label mb-0">Select Project:</label>
                <select name="project" id="project" class="form-select" style="width: auto;" onchange="this.form.submit()">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $selectedProject == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Task List -->
        <div id="task-list" class="sortable-list">
            @forelse($tasks as $task)
                <div class="card task-item mb-2" data-task-id="{{ $task->id }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary priority-badge">P{{ $task->priority }}</span>
                                    <h6 class="mb-0">{{ $task->name }}</h6>
                                    @if($task->project)
                                        <small class="text-muted">
                                            <i class="fas fa-folder"></i> {{ $task->project->name }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="editTask({{ $task->id }}, '{{ $task->name }}', '{{ $task->project_id }}', '{{ $task->priority }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete this task?')" 
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No tasks found</h4>
                    <p class="text-muted">Create your first task to get started!</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Instructions</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-plus text-success"></i> Click "Add Task" to create a new task</li>
                    <li class="mb-2"><i class="fas fa-arrows-alt text-primary"></i> Drag and drop tasks to reorder them</li>
                    <li class="mb-2"><i class="fas fa-edit text-warning"></i> Click edit button to modify a task</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Task Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            @for ($i = 1; $i <= ($taskCount + 1); $i++)
                                <option value="{{ $i }}" {{ ($taskCount +1) == $i ? 'selected' : '' }}>
                                    P{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">-- select --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '11' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTaskForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Task Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_priority" name="priority" required>
                            @for ($i = 1; $i <= $taskCount; $i++)
                                <option value="{{ $i }}" {{ $taskCount == $i ? 'selected' : '' }}>
                                    P{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_project_id" class="form-label">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_project_id" name="project_id">
                            <option value="">-- select --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable
    const taskList = document.getElementById('task-list');
    if (taskList) {
        new Sortable(taskList, {
            animation: 150,
            ghostClass: 'task-item-ghost',
            onEnd: function(evt) {
                const taskIds = Array.from(taskList.children).map(item => item.dataset.taskId);
                fetch('{{ route("tasks.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        task_ids: taskIds
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update priority badges
                        taskList.querySelectorAll('.priority-badge').forEach((badge, index) => {
                            badge.textContent = 'P' + (index + 1);
                        });
                    }
                }).catch(error => console.error('Error:', error));
            }
        });
    }
});

function editTask(id, name, projectId, priority) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_project_id').value = projectId || '';
    document.getElementById('edit_priority').value = priority || '';
    document.getElementById('editTaskForm').action = '/tasks/' + id;

    const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    modal.show();
}
</script>
@endsection
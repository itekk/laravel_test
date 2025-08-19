@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-folder"></i> Projects</h2>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>

        <div class="row">
            @forelse($projects as $project)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title">{{ $project->name }}</h5>
                                    @if($project->description)
                                        <p class="card-text text-muted">{{ $project->description }}</p>
                                    @endif
                                    <small class="text-muted">
                                        <i class="fas fa-tasks"></i> {{ $project->tasks_count }} tasks
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editProject({{ $project->id }}, '{{ $project->name }}', '{{ addslashes($project->description) }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}" 
                                          onsubmit="return confirm('Are you sure? This will also delete all associated tasks.')" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('tasks.index', ['project' => $project->id]) }}" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i> View Tasks
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-folder fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No projects found</h4>
                        <p class="text-muted">Create your first project to organize your tasks!</p>
                    </div>
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
                    <li class="mb-2"><i class="fas fa-plus text-success"></i> Create new projects</li>
                    <li class="mb-2"><i class="fas fa-edit text-warning"></i> Edit existing projects</li>
                    <li class="mb-2"><i class="fas fa-eye text-info"></i> View tasks by project</li>
                    <li class="mb-2"><i class="fas fa-trash text-danger"></i> Delete projects</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editProjectForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Project</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editProject(id, name, description) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('editProjectForm').action = '/projects/' + id;

    const modal = new bootstrap.Modal(document.getElementById('editProjectModal'));
    modal.show();
}
</script>
@endsection
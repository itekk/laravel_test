<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /* list tasks */
    public function index(Request $request)
    {
        $projects        = Project::all();
        $firstProject    = Project::first();
        $selectedProject = $request->get('project') ?? ($firstProject ? $firstProject->id : null);
        $query           = Task::with('project')->orderBy('priority');

        if ($selectedProject) {
            $query->where('project_id', $selectedProject);
        }

        $tasks     = $query->get();
        $taskCount = $tasks->count() ?? 1;

        return view('tasks.index', compact('tasks', 'projects', 'selectedProject', 'taskCount'));
    }

    /* create a task */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'priority'   => 'required|integer|min:1',
        ]);

        try {
            Task::createWithPriority($request->only(['name', 'project_id', 'priority']));
            return redirect()->back()->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create task', [
                'error' => $e->getMessage(),
                'request_data' => $request->only(['name', 'project_id', 'priority'])
            ]);
            
            return redirect()->back()->with('error', 'Failed to create task. Please try again.');
        }
    }

    /* update a task */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'priority'   => 'required|integer|min:1',
        ]);

        try {
            $task->updateWithPriority($request->only(['name', 'project_id', 'priority']));
            return redirect()->back()->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update task', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'request_data' => $request->only(['name', 'project_id', 'priority'])
            ]);
            
            return redirect()->back()->with('error', 'Failed to update task. Please try again.');
        }
    }

    /* delete a task */
    public function destroy(Task $task)
    {
        try {
            $task->deleteWithPriority();
            return redirect()->back()->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete task', [
                'task_id' => $task->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to delete task. Please try again.');
        }
    }

    /* reorder a task priority */
    public function reorder(Request $request)
    {
        $request->validate([
            'task_ids'   => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
        ]);

        try {
            Task::reorderByIds($request->task_ids);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to reorder tasks', [
                'error' => $e->getMessage(),
                'task_ids' => $request->task_ids
            ]);
            
            return response()->json(['success' => false, 'message' => 'Failed to reorder tasks'], 500);
        }
    }
}

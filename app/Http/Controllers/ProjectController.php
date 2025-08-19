<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /* list projects */
    public function index()
    {
        $projects = Project::withTaskCounts()->get();
        return view('projects.index', compact('projects'));
    }

    /* create a project */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            Project::create($request->only(['name', 'description']));
            return redirect()->route('projects.index')->with('success', 'Project created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create project', [
                'error' => $e->getMessage(),
                'request_data' => $request->only(['name', 'description'])
            ]);
            
            return redirect()->back()->with('error', 'Failed to create project. Please try again.');
        }
    }

    /* update a project */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $project->update($request->only(['name', 'description']));
            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update project', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'request_data' => $request->only(['name', 'description'])
            ]);
            
            return redirect()->back()->with('error', 'Failed to update project. Please try again.');
        }
    }

    /* delete a project */
    public function destroy(Project $project)
    {
        try {
            $project->deleteWithTasks();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete project', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to delete project. Please try again.');
        }
    }
}

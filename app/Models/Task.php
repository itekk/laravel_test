<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'project_id',
    ];

    protected $casts = [
        'priority' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Create a new task with proper priority management
     */
    public static function createWithPriority(array $data)
    {
        return DB::transaction(function () use ($data) {
            $maxPriority = static::where('project_id', $data['project_id'])->max('priority') ?? 0;
            
            // If priority is specified, make room for it
            if (isset($data['priority'])) {
                static::where('project_id', $data['project_id'])
                    ->where('priority', '>=', $data['priority'])
                    ->increment('priority');
            } else {
                $data['priority'] = $maxPriority + 1;
            }
            
            return static::create($data);
        });
    }

    /**
     * Update task with priority management
     */
    public function updateWithPriority(array $data)
    {
        return DB::transaction(function () use ($data) {
            $oldProjectId = $this->project_id;
            $oldPriority  = $this->priority;
            $newProjectId = $data['project_id'];
            $newPriority  = $data['priority'];

            $this->adjustPrioritiesForUpdate($oldProjectId, $oldPriority, $newProjectId, $newPriority);
            
            return $this->update($data);
        });
    }

    /**
     * Delete task with priority reordering
     */
    public function deleteWithPriority()
    {
        return DB::transaction(function () {
            $projectId = $this->project_id;
            $priority = $this->priority;
            
            $this->delete();
            
            // Reorder remaining tasks
            static::where('project_id', $projectId)
                ->where('priority', '>', $priority)
                ->decrement('priority');
                
            return true;
        });
    }

    /**
     * Reorder tasks by array of IDs
     */
    public static function reorderByIds(array $taskIds)
    {
        return DB::transaction(function () use ($taskIds) {
            foreach ($taskIds as $index => $taskId) {
                static::where('id', $taskId)->update(['priority' => $index + 1]);
            }
        });
    }

    /**
     * Adjust priorities when updating a task
     */
    private function adjustPrioritiesForUpdate($oldProjectId, $oldPriority, $newProjectId, $newPriority)
    {
        // Same project, priority changed
        if ($oldProjectId == $newProjectId && $oldPriority != $newPriority) {
            $this->adjustPrioritiesWithinProject($oldProjectId, $oldPriority, $newPriority);
        }

        // Project changed
        if ($oldProjectId != $newProjectId) {
            $this->adjustPrioritiesForProjectChange($oldProjectId, $oldPriority, $newProjectId, $newPriority);
        }
    }

    /**
     * Adjust priorities within the same project
     */
    private function adjustPrioritiesWithinProject($projectId, $oldPriority, $newPriority)
    {
        if ($newPriority > $oldPriority) {
            // Moving down — shift up tasks between old and new
            static::where('project_id', $projectId)
                ->where('priority', '>', $oldPriority)
                ->where('priority', '<=', $newPriority)
                ->decrement('priority');
        } else {
            // Moving up — shift down tasks between new and old
            static::where('project_id', $projectId)
                ->where('priority', '>=', $newPriority)
                ->where('priority', '<', $oldPriority)
                ->increment('priority');
        }
    }

    /**
     * Adjust priorities when moving task between projects
     */
    private function adjustPrioritiesForProjectChange($oldProjectId, $oldPriority, $newProjectId, $newPriority)
    {
        // Adjust old project (remove this task's priority)
        static::where('project_id', $oldProjectId)
            ->where('priority', '>', $oldPriority)
            ->decrement('priority');

        // Adjust new project (make room)
        static::where('project_id', $newProjectId)
            ->where('priority', '>=', $newPriority)
            ->increment('priority');
    }
}

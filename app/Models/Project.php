<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('priority');
    }

    /**
     * Delete project with all associated tasks
     */
    public function deleteWithTasks()
    {
        return DB::transaction(function () {
            // Delete all tasks associated with this project
            $this->tasks()->delete();
            
            // Delete the project
            return $this->delete();
        });
    }

    /**
     * Get projects with task counts
     */
    public static function withTaskCounts()
    {
        return static::withCount('tasks');
    }
}

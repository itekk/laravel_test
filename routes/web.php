<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/', [TaskController::class, 'index'])->name('home');
Route::resource('tasks', TaskController::class)->except(['show', 'create', 'edit']);
Route::resource('projects', ProjectController::class)->except(['show', 'create', 'edit']);
Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');

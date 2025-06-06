<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    public function index()
    {
        return Task::all();
    }
    public function store(Request $request)
    {
        $fields = $request->validate([
            "title" => 'required',
            "description" => 'required',
            "due_date" => 'required',
            "status_id" => 'required',
            "category_id" => 'required',
        ]);

        $task = $request->user()->tasks()->create($fields);

        $task->status()->updateOrCreate(
            ['post_id' => $task->id],
            ['status' => $request->input('status')]
        );

        return $task;
    }

    public function show(Task $task)
    {
        return Task::with('status')->find($task->id);
    }

    public function update(Request $request, Task $task)
    {
        Gate::authorize('change', $

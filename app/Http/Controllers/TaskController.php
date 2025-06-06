<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            "title" = 'required '
            "discription" = 'required '
            "due_date" = 'required '
            "status_id" = 'required '
            "catagorya_id" = 'required '
        ]);

        $post =  $request->user()->posts()->create([
            'title' => $request->input('title'),
            'discription' => $request->input('discription')
            'due_date' => $request->input('due_date'),
            'status_id' => $request->input('status_id')
            'catagorya_id' => $request->input('catagorya_id'),
        ]);
        $post->status()->updateOrCreate(
            ['post_id' => $post->id],
            ['status' => $request->input('status')] // or 'private'
        );

        return $post;
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post = Post::with('status')->find($post->id);
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('change', $post);
        $fields = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required'
        ]);

        $post->update($fields);

        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('change', $post);
        $post->delete();
        return ['message' => "The post ($post->id) has been deleted"];
    }
}
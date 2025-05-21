<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Exception;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Post::with('user');

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('body', 'like', "%$search%");
                });
            }

            if ($request->has('author_id')) {
                $query->where('user_id', $request->input('author_id'));
            }

            $posts = $query->paginate($request->input('per_page', 10));

            return response()->json($posts);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching posts', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'body' => 'required|string',
            ]);

            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Post creation failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::with('user')->find($id);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }
            return response()->json(['post' => $post]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching post', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Post::find($id);
            if (!$post || $post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized or not found'], 403);
            }

            $post->update($request->only(['title', 'body']));
            return response()->json(['message' => 'Post updated', 'post' => $post]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Post update failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $post = Post::find($id);
            if (!$post || $post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized or not found'], 403);
            }

            $post->delete();
            return response()->json(['message' => 'Post deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Post deletion failed', 'error' => $e->getMessage()], 500);
        }
    }
}

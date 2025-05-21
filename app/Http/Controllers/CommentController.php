<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CommentController extends Controller
{
    public function index($postId)
    {
        try {
            $post = Post::find($postId);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $comments = $post->comments()->with('user:id,name,email')->latest()->get();

            return response()->json(['comments' => $comments], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $postId)
    {
        try {
            $request->validate([
                'body' => 'required|string',
                'content' => 'required|string',
            ]);

            $post = Post::find($postId);
            if (!$post) {
                return response()->json(['message' => 'Post not found'], 404);
            }

            $comment = Comment::create([
                'body' => $request->body,
                'user_id' => Auth::id(),
                'post_id' => $postId,
                'content' => $request->content,
            ]);

            return response()->json([
                'message' => 'Comment added successfully',
                'comment' => $comment
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to add comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::find($id);

            if (!$comment || $comment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized or not found'], 403);
            }

            $request->validate([
                'content' => 'required|string',
            ]);

            $comment->update([
                'content' => $request->content,
            ]);

            return response()->json([
                'message' => 'Comment updated successfully',
                'content' => $comment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::find($id);

            if (!$comment || $comment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized or not found'], 403);
            }

            $comment->delete();

            return response()->json(['message' => 'Comment deleted']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



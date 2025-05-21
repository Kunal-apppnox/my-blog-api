<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();

        $like = Like::where('post_id', $post->id)->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['message' => 'Post unliked'], 200);
        }

        Like::create(['post_id' => $post->id, 'user_id' => $user->id]);
        return response()->json(['message' => 'Post liked'], 201);
    }

    public function totalLikes($postId)
    {
        $count = Like::where('post_id', $postId)->count();
        return response()->json(['post_id' => $postId, 'likes' => $count], 200);
    }
}

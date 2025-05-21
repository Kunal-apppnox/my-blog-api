<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LikeController extends Controller
{
    public function toggleLike($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            //print_r($post);die;
            $user = Auth::user();

            $existingLike = Like::where('post_id', $post->id)
                               // ->where('user_id', $user->id)
                                ->first();

            if ($existingLike) {
                $existingLike->delete();
                return response()->json([
                    'message' => 'Post unliked'
                ], 200);
            }

            Like::create([
                'post_id' => $post->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'message' => 'Post liked'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to toggle like',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function totalLikes($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $count = Like::where('post_id', $postId)->count();

            return response()->json([
                'post_id' => $postId,
                'total_likes' => $count
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch likes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

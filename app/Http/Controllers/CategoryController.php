<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json($categories, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories'
            ]);

            $category = Category::create(['name' => $request->name]);

            return response()->json([
                'message' => 'Category created',
                'category' => $category
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|unique:categories,name,' . $id
            ]);

            $category->update(['name' => $request->name]);

            return response()->json([
                'message' => 'Category updated',
                'category' => $category
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Category::destroy($id);

            return response()->json(['message' => 'Category deleted'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignCategoriesToPost(Request $request, $postId)
    {
        try {
            $request->validate([
                'category_ids' => 'required|array|exists:categories,id',
                // 'category_ids.*' => 'exists:categories,id'
            ]);

            $post = Post::findOrFail($postId);
            $post->categories()->sync($request->category_ids);

            return response()->json(['message' => 'Categories assigned to post successfully']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to assign categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPostsByCategory($categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $posts = $category->posts()->with('user')->get();

            if ($posts->isEmpty()) {
                return response()->json([
                    'message' => 'No posts found for this category'
                ], 404);
            }

            return response()->json([
                'category_id' => $categoryId,
                'category_name' => $category->name,
                'posts' => $posts
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch posts for category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}

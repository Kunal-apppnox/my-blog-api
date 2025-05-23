<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;





class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of categories",
     *     description="Returns a list of all categories from the database.",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *  security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="application/json"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="HealthCare"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-21T07:24:25.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-21T07:24:25.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to fetch categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to fetch categories"),
     *             @OA\Property(property="error", type="string", example="Some error details")
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $categories = Category::all();

            return response()->json([
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    //Listing api using using cache (at the time of implementing redis)
    // public function index()
    // {
    //     try {
    //         if (Cache::has('categories_list')) {
    //             $categories = Cache::get('categories_list');
    //             $source = 'cache';
    //         } else {
    //             $categories = Category::all();
    //             Cache::put('categories_list', $categories, ttl: 3600); // Store in cache
    //             $source = 'database';
    //         }

    //         return response()->json([
    //             'source' => $source,
    //             'data' => $categories
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Failed to fetch categories',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="application/json"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="DIY")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category created"),
     *             @OA\Property(
     *                 property="category",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=4),
     *                 @OA\Property(property="name", type="string", example="DIY"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-23T07:36:42.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-23T07:36:42.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="error", type="string", example="Exception details")
     *         )
     *     )
     * )
     */



    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:categories'
            ]);

            $category = Category::create(['name' => $request->name]);
            Cache::forget('categories_list');

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

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         example="application/json"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category updated"),
     *             @OA\Property(
     *                 property="category",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="Macbook updated"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-21T07:26:05.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-23T07:44:24.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */


    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|unique:categories,name,' . $id
            ]);

            $category->update(['name' => $request->name]);
            Cache::forget('categories_list');

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

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */


    public function destroy($id)
    {
        try {
            Category::destroy($id);
            Cache::forget('categories_list');

            return response()->json(['message' => 'Category deleted'], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/posts/{postId}/categories",
     *     summary="Assign categories to a post",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="postId",
     *         in="path",
     *         required=true,
     *         description="Post ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category_ids"},
     *             @OA\Property(
     *                 property="category_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categories assigned to post successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */

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


    /**
     * @OA\Get(
     *     path="/api/categories/{categoryId}/posts",
     *     summary="Get posts by category ID",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Posts under category fetched successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No posts found or category not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */


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



















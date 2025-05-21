<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   
use App\Http\Requests;
use App\Models\Test;
use Exception;

class TestController extends Controller
{
    public function store(Request $request)
    {
        try{
            $request->validate([
                'Name' => 'required|string',
                'Age' => 'required|integer',
            ]);
            

            $test = Test::create([
                'Name'=> $request->Name,
                'Age'=> $request->Age,
            ]);

            return response()->json([
                'message' => 'Created Successfully.'
            ], 200); 

        }catch(Exception $e){
            echo $e->getMessage();
            return response()->json(['message' => 'Invalid  Data Entered'], 500);
        }
    }

    public function show($id)
    {
        try {
            $test = Test::findOrFail($id);
            if (!$test) {
                return response()->json(['message' => 'Test not found'], 404);
            }
            return response()->json(['test' => $test]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching test', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'Name' => 'required|string',
            'Age' => 'required|integer',
        ]);

        $test = Test::find($id);
        // print_r($test);die;
        if (!$test) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $test->update([
            'Name' => $request->Name,
            'Age' => $request->Age,
        ]);

        return response()->json(['message' => 'Post updated', 'test' => $test]);

    } catch (Exception $e) {
        return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
    }
}


    public function destroy(Request $request, $id)
    {
        try {
            $test = Test::find($id);
            $test->delete();
            return response()->json(['message' => 'Test deleted successfully']);
        }catch(Exception $e){
            return response()->json(['message'=> 'Not Deleted', 'error' => $e->getMessage()], 500);
        }
    }
}

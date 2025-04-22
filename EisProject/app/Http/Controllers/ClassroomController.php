<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'classrooms' => Classroom::all(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:classrooms,name',
            'capacity' => 'required|integer',
            'type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $validator->validated();
        $classroom = Classroom::create($inputs);

        if (!$classroom) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to create classroom',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'classroom' => $classroom,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:classrooms,name,' . $id,
            'capacity' => 'sometimes|required|integer',
            'type' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $validator->validated();
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Classroom not found',
            ], 404);
        }

        $classroom->update($inputs);

        return response()->json([
            'status' => 'success',
            'classroom' => $classroom,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classroom = Classroom::find($id);

        if (!$classroom) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Classroom not found',
            ], 404);
        }

        $classroom->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Classroom deleted successfully',
        ], 200);
    }
}
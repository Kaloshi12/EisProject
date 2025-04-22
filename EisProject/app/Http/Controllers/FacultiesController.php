<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;

class FacultiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return response()->json([
            'status' => 'success',
            'faculties' => Faculty::all(),
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:faculties,name',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $faculty = Faculty::create($inputs);
        if (!$faculty) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to create faculty',
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'faculty' => $faculty,
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
            'name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Faculty not found',
            ], 404);
        }
        $faculty->update($inputs);
        $faculty->save();
        return response()->json([
            'status' => 'success',
            'faculty' => $faculty,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $faculty = Faculty::find($id);
        if (!$faculty) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Faculty not found',
            ], 404);
        }
        $faculty->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Faculty deleted successfully',
        ], 200);
    }
}
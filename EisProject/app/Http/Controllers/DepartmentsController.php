<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\Faculties;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'departments' => Departments::all(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:departments,name',
            'faculty_name' => 'required|string|max:255|exists:faculties,name',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $faculty = Faculty::where('name', $inputs['faculty_name'])->first();
        if (!$faculty) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Faculty not found',
            ], 404);
        }
        $department = Departments::create([
            'name' => $inputs['name'],
            'faculty_id' => $faculty->id,
        ]);
        if (!$department) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to create department',
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'department' => $department,
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
            'faculty_name' => 'required|string|max:255|exists:faculties,name',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $department = Departments::find($id);
        if (!$department) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Department not found',
            ], 404);
        }
        $faculty = Faculty::where('name', $inputs['faculty_name'])->first();
        if (!$faculty) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Faculty not found',
            ], 404);
        }
        $department->update([
            'name' => $inputs['name'],
            'faculty_id' => $faculty->id,
        ]);
        return response()->json([
            'status' => 'success',
            'department' => $department,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $department = Departments::find($id);
        if (!$department) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Department not found',
            ], 404);
        }
        $department->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Department deleted successfully',
        ], 200);
    }
}
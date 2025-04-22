<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DegreesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        return response()->json([
            'status' => 'success',
            'degrees' => Degree::all(),
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:degrees,name',
            'cost' => 'required|float',
            'is_graduated' => 'required|boolean',
            'deaprtment_name' => 'required|string|max:255|exists:departments,name',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $inputs = $validator->validated();
        $department = Departments::where('name', $inputs['deaprtment_name'])->first();
        if (!$department) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Department not found',
            ], 404);
        }
        
        $degree = Degree::create([
            'name' => $inputs['name'],
            'cost' => $inputs['cost'],
            'is_graduated' => $inputs['is_graduated'],
            'department_id' => $department->id,
        ]);
        if (!$degree) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to create degree',
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'degree' => $degree,
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
            'name' => 'sometimes|string|max:255',
            'cost' => 'sometimes|float',
            'is_graduated' => 'sometimes|boolean',
            'deaprtment_name' => 'sometimes|string|max:255|exists:departments,name',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $inputs = $validator->validated();
        $degree = Degree::find($id);
        if (!$degree) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Degree not found',
            ], 404);
        }
        
        $degree->update($inputs);
        return response()->json([
            'status' => 'success',
            'degree' => $degree,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $degree = Degree::find($id);
        if (!$degree) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Degree not found',
            ], 404);
        }
        
        $degree->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Degree deleted successfully',
        ], 200);
    }
}
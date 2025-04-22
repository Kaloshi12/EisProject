<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'class_groups' => ClassGroup::all(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:class_groups,name',
            'nr_max_students' => 'required|integer',
            'year_study' => 'required|integer',
            'degree_name' => 'required|string|max:255|exists:degrees,name',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $degree = Degree::where('name', $inputs['degree_name'])->first();
        if (!$degree) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Degree not found',
            ], 404);
        }
        $classGroup = ClassGroup::create([
            'name' => $inputs['name'],
            'nr_max_students' => $inputs['nr_max_students'],
            'year_study' => $inputs['year_study'],
            'degree_id' => $degree->id,
        ]);
        if (!$classGroup) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to create class group',
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'class_group' => $classGroup,
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
            'name' => 'string|max:255',
            'nr_max_students' => 'integer',
            'year_study' => 'integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $classGroup = ClassGroup::find($id);
        if (!$classGroup) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Class group not found',
            ], 404);
        }
        $classGroup->update($inputs);
        return response()->json([
            'status' => 'success',
            'class_group' => $classGroup,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $classGroup = ClassGroup::find($id);
        if (!$classGroup) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Class group not found',
            ], 404);
        }
        $classGroup->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Class group deleted successfully',
        ], 200);
    }
}
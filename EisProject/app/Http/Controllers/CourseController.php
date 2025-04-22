<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Degree;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\ErrorHandler\Debug;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'all courses',
            'data' => Course::all(),
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validators =Validator::make($request->all(),[
            
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'credits' => 'required|integer',
                'etc' => 'nullable|integer',
                'category' => 'required|in:compulsory,elective,non-technical,technical',
                'semester' => 'required|integer',
                'degree' => 'required|string|max:255',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validators->errors(),
            ], 422);
        }
        $inputs = $validators->validated();
        $degree = Degree::where('name', $inputs['degree'])->first();
        if (!$degree) {
            return response()->json([
                'message' => 'Degree not found',
            ], 404);
        }
        
        $course = Course::create([
            'code' => $inputs['code'],
            'name' => $inputs['name'],
            'credits' => $inputs['credits'],
            'etc' => $inputs['etc'],
            'category' => $inputs['category'],
            'semester' => $inputs['semester'],
            'degree_id' => $degree->id,
        ]);
        if (!$course) {
            return response()->json([
                'message' => 'Failed to create course',
            ], 500);
        }
        return response()->json([
            'message' => 'Course created successfully',
            'data' => $course,
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
        $validators = Validator::make($request->all(), [
            'code' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'credits' => 'sometimes|integer',
            'etc' => 'nullable|integer',
            'category' => 'sometimes|in:compulsory,elective,non-technical,technical',
            'semester' => 'sometimes|required|integer',  // Fixed line here
            'degree' => 'sometimes|string|max:255',
        ]);
        

        if ($validators->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validators->errors(),
            ], 422);
        }
        $inputs = $validators->validated();
        
        $degree = Degree::where('name', $inputs['degree'])->first();
        if (!$degree) {
            return response()->json([
                'message' => 'Degree not found',
            ], 404);
        }
        
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
            ], 404);
        }
        
        $course->update([
            'code' => $inputs['code'],
            'name' => $inputs['name'],
            'credits' => $inputs['credits'],
            'etc' => $inputs['etc'],
            'category' => $inputs['category'],
            'semester' => $inputs['semester'],
            'degree_id' => $degree->id,
        ]);
        
        return response()->json([
            'message' => 'Course updated successfully',
            'data' => $course,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => 'Course not found',
            ], 404);
        }
        
        $course->delete();
        
        return response()->json([
            'message' => 'Course deleted successfully',
        ], 200);
    }
}
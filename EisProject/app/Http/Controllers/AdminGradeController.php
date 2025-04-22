<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'List of Grades',
            'grades' => Grade::all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string',
            'student_name' => 'required|string',
            'student_surname' => 'required|string',
            'lecturer_name' => 'required|string',
            'lecturer_surname' => 'required|string',
            'type' => 'required|string',
            'weight' => 'required|float',
            'points' => 'required|float'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();
        $course = Course::where('name', $inputs['course_name'])->first();
        if (!$course) {
            return response()->json([
                'message' => 'Course not found'
            ], 404);
        }
        $student = User::where('name', $inputs['student_name'])->when('surname', $inputs['student_surname'])->where('role_id', config('constants.STUDENT_ROLE_ID'))->first();
        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        $lecturer = User::where('name', $inputs['lecturer_name'])->when('surname', $inputs['lecturer_surname'])->where('role_id', config('constants.LECTURER_ROLE_ID'))->first();
        if (!$lecturer) {
            return response()->json([
                'message' => 'Lecturer not found'
            ], 404);
        }

        $grade = Grade::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'lecturer' => $lecturer->id,
            'type' => $inputs['type'],
            'weight' => $inputs['wight'],
            'points' => $inputs['points']
        ]);
        if (!$grade) {
            return response()->json([
                'message' => 'failed to create a grade'
            ], 500);
        }
        return response()->json([
            'message' => 'Grade added succesfully',
            'grade' => $grade
        ], 200);
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
            'course_name' => 'sometimes|string',
            'student_name' => 'sometimes|string',
            'student_surname' => 'sometimes|string',
            'lecturer_name' => 'sometimes|string',
            'lecturer_surname' => 'sometimes|string',
            'type' => 'sometimes|string',
            'weight' => 'sometimes|numeric',
            'points' => 'sometimes|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        $inputs = $validator->validated();

        $grade = Grade::find($id);
        if (!$grade) {
            return response()->json(['message' => 'Grade not found'], 404);
        }
        $updateData = [];

        if (!empty($inputs['course_name'])) {
            $course = Course::where('name', $inputs['course_name'])->first();
            if (!$course) {
                return response()->json(['message' => 'Course not found'], 404);
            }
            $updateData['course_id'] = $course->id;
        }

        if (!empty($inputs['student_name']) && !empty($inputs['student_surname'])) {
            $student = User::where('name', $inputs['student_name'])
                ->where('surname', $inputs['student_surname'])
                ->where('role_id', config('constants.STUDENT_ROLE_ID'))
                ->first();

            if (!$student) {
                return response()->json(['message' => 'Student not found'], 404);
            }
            $updateData['student_id'] = $student->id;
        }

        if (!empty($inputs['lecturer_name']) && !empty($inputs['lecturer_surname'])) {
            $lecturer = User::where('name', $inputs['lecturer_name'])
                ->where('surname', $inputs['lecturer_surname'])
                ->where('role_id', config('constants.LECTURER_ROLE_ID'))
                ->first();

            if (!$lecturer) {
                return response()->json(['message' => 'Lecturer not found'], 404);
            }
            $updateData['lecturer_id'] = $lecturer->id;
        }

        foreach (['type', 'weight', 'points'] as $field) {
            if (array_key_exists($field, $inputs)) {
                $updateData[$field] = $inputs[$field];
            }
        }

        $grade->update($updateData);

        return response()->json([
            'message' => 'Grade updated successfully',
            'grade' => $grade->fresh(['student', 'lecturer', 'course']),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $grade = Grade::find($id);
        if (!$grade) {
            return response()->json(['message' => 'Grade not found'], 404);
        }
        $grade->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Grade deleted successfully',
        ], 200);

    }
}
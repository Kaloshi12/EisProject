<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Course;
use App\Models\CourseHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'All courses hours',
            'course hours' => CourseHours::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday',
            'start_hour' => 'required|date_format:H:i',
            'num_hours' => 'required|integer|min:1|max:10',
            'category' => 'required|in:theory,lab,seminar',
            'class_group_name' => 'required|string',
        ]);

        // Handle validation errors explicitly
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inputs = $validator->validated();

        // Find course and group
        $course = Course::where('name', $inputs['course_name'])->first();
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $group = ClassGroup::where('name', $inputs['class_group_name'])->first();
        if (!$group) {
            return response()->json(['message' => 'Class group not found'], 404);
        }

        // Create course hours record
        $courseHours = CourseHours::create([
            'course_id' => $course->id,
            'day' => $inputs['day'],
            'start_hour' => $inputs['start_hour'],
            'num_hours' => $inputs['num_hours'],
            'category' => $inputs['category'],
            'class_group_id' => $group->id
        ]);

        return response()->json([
            'message' => 'Success',
            'timetable' => $courseHours
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
            'day' => 'sometimes|in:monday,tuesday,wednesday,thursday,friday',
            'start_hour' => 'sometimes|date_format:H:i',
            'num_hours' => 'sometimes|integer|min:1|max:10',
            'category' => 'sometimes|in:theory,lab,seminar',
            'class_group_name' => 'sometimes|string',
        ]);
        $inputs = $validator->validated();
        $courseHour = CourseHours::findOrFail($id);
        if (isset($inputs['course_name'])) {
            $course = Course::where('name', $inputs['course_name'])->first();
            if (!$course) {
                return response()->json(['message' => 'Course does not exist with this name'], 404);
            }
            $courseHour->course_id = $course->id;
        }

        if (isset($inputs['class_group_name'])) {
            $group = ClassGroup::where('name', $inputs['class_group_name'])->first();
            if (!$group) {
                return response()->json(['message' => 'Group does not exist with this name'], 404);
            }
            $courseHour->class_group_id = $group->id;
        }
        foreach (['day', 'start_hour', 'num_hours', 'category'] as $field) {
            if (isset($inputs[$field])) {
                $courseHour->$field = $inputs[$field];
            }
        }

        $courseHour->save();

        return response()->json([
            'message' => 'Course hour updated successfully',
            'data' => $courseHour
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $courseHours = CourseHours::find($id);
        if (!$courseHours) {
            return response()->json(['message' => 'this record does not exist with this name'], 404);
        }
        $courseHours->delete();
        return response()->json(['message' => 'this record deleted successfully'], 202);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'all attendance record',
            'attendnace' => Attendance::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lecturer_name' => 'required|string',
            'lecturer_surname' => 'required|string',
            'course_name' => 'required|string',
            'student_name' => 'required|string',
            'student_surname' => 'required|string',
            'hours_attended' => 'required|integer',
            'topic' => 'required|string',
            'category' => 'required|in:lesson,lab,seminar',
            'number_hours' => 'required|integer',
            'week' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $inputs = $validator->validated();

        // Fetch Lecturer
        $lecturer = User::where('name', $inputs['lecturer_name'])
            ->where('surname', $inputs['lecturer_surname'])
            ->first();

        if (!$lecturer) {
            return response()->json(['message' => 'Lecturer not found'], 404);
        }

        if ($lecturer->role->name != 'lecturer') {
            return response()->json(['message' => 'User is not a lecturer'], 403);
        }

        // Fetch Course
        $course = Course::where('name', $inputs['course_name'])->first();
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // Check Date
        if (Carbon::now()->lt($inputs['date'])) {
            return response()->json([
                'message' => "Cannot record attendance for future date: " . $inputs['date']
            ], 422);
        }

        // Check Lecturer Authorization
        $year = Carbon::now()->year;
        $isAuthorized = $lecturer->lecturersCourses()->where('course_id', $course->id)
            ->where('academic_year', $year)
            ->where('is_active', true)
            ->exists()
            || $lecturer->assistantsCourses()->where('course_id', $course->id)
                ->where('academic_year', $year)
                ->where('is_active', true)
                ->exists();

        if (!$isAuthorized) {
            return response()->json(['message' => 'Unauthorized for this course'], 403);
        }



        $student = User::where('name', $inputs['student_name'])->where('surname', $inputs['student_surname'])->where('role_id', config('constants.STUDENT_ROLE_ID'))->first();

        $isEnrolled = DB::table('course_user')
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->where('role', 'student')
            ->where('status', 'enrolled')
            ->exists();

        if (!$isEnrolled) {

            return response()->json([
                'message' => "$student->name  $student->surname  is not enrolled to this course"
            ]);
        }

        $attendance = Attendance::create([
            'lecturer_id' => $lecturer->id,
            'course_id' => $course->id,
            'student_id' => $student->id,
            'topic' => $inputs['topic'],
            'category' => $inputs['category'],
            'hours_attended' => $inputs['hours_attended'],
            'number_hours' => $inputs['number_hours'],
            'week' => $inputs['week'],
            'date' => $inputs['date']
        ]);

        if (!$attendance) {
            return response()->json(['message' => 'Failed to record attendance for student'], 500);
        }
        return response()->json(['message' => 'Attendance recorded successfully'], 201);
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
            'lecturer_name' => 'sometimes|string',
            'lecturer_surname' => 'sometimes|string',
            'course_name' => 'sometimes|string',
            'student_name' => 'sometimes|string',
            'student_surname' => 'sometimes|string',
            'topic' => 'sometimes|string',
            'category' => 'sometimes|in:lesson,lab,seminar',
            'hours_attended' => 'sometimes|integer',
            'number_hours' => 'sometimes|integer',
            'week' => 'sometimes|integer',
            'date' => 'sometimes|date_format:Y-d-m',
        ]);

        $inputs = $validator->validated();

        $lecturer = User::where('name', $inputs['lecturer_name'])
            ->where('surname', $inputs['lecturer_surname'])
            ->first();

        if (!$lecturer) {
            return response()->json([
                'message' => "this lecturer not found"
            ], 404);
        }
        if ($lecturer->role->name = !'lecturer') {
            return response()->json([
                'message' => 'This user is not a lecturer and cannot make attendance'
            ]);
        }
        $course = Course::where('name', $inputs['course_name'])->first();
        if (!$course) {
            return response()->json([
                'message' => 'No course found with this name'
            ]);
        }
        $isLecturer = $lecturer->lecturersCourses()->where('course_id', $course->id)->exists();
        $isAsistant = $lecturer->assistantsCourses()->where('course_id', $course->id)->exists();
        if (!$isLecturer || !$isAsistant) {
            return response()->json([
                'message' => 'This user is not autherized to record attandance for this course'
            ]);
        }
        $student = User::where('name', $inputs['student_name'])
            ->where('surname', $inputs['student_surname'])
            ->first();
        if (!$student) {
            return response()->json([
                'message' => 'Student not Found'
            ], 404);
        }
        $isEnrolled = DB::table('course_user')
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->where('role', 'student')
            ->where('status', 'enrolled')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'This student is not enrolled'
            ]);
        }
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json([
                'message' => 'Attendance record not found'
            ], 404);
        }

        $attendance->update(collect($inputs)->only([
            'topic',
            'category',
            'hours_attended',
            'number_hours',
            'week',
            'date'
        ])->toArray());

        return response()->json([
            'message' => 'Attendance updated successfully',
            'attendance' => $attendance
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Cannot find a record attendance with this id',
            ], 404);
        }
        $attendance->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Recorded attendance deleted successfully',
        ], 200);
    }
}

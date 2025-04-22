<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\CrossJoinSequence;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function publishGrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_name' => 'required|string',
            'student_name' => 'required|string',
            'student_surname' => 'required|string',
            'type' => 'required|string',
            'weight' => 'required|numeric',
            'points' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputs = $validator->validated();
        $user = Auth::user();
        $year = Carbon::now()->year;
        $course = Course::where('name', $inputs['course_name'])->first();

        $isLecturer = $user->lecturersCourses()->where('course_id', $course->id)->where('academic_year', $year)->where('is_active', true)->exists();


        $isAssistant = $user->assistantsCourses()->where('course_id', $course->id)->where('academic_year', $year)->where('is_active', true)->exists();

        if (!$isLecturer) {
            if (!$isAssistant) {
                return response()->json([
                    'message' => 'Unauthorized for this course'
                ], 403);


            }
        }



        $student = User::where('name', $inputs['student_name'])
            ->where('surname', $inputs['student_surname'])->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        $year = Carbon::now()->year;


        $isEnrolled = DB::table('course_user')
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->where('role', 'student')
            ->where('academic_year', $year)
            ->where('status', 'enrolled')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'message' => 'Student is not enrolled in this course'
            ]);
        }

        $grade = Grade::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'lecture_id' => $user->id,
            'type' => $inputs['type'],
            'weight' => $inputs['weight'],
            'points' => $inputs['points'],
            'is_seen' => false
        ]);

        if (!$grade) {
            return response()->json([
                'message' => "Failed to publish this grade by {$user->name} {$user->surname}"
            ]);
        }

        return response()->json([
            'message' => 'Grade published successfully',
            'grade' => $grade,
            'by' => "{$user->name} {$user->surname}"
        ]);
    }


    public function seeGradesByLecture()
    {
        $user = Auth::user();
        $lecturerCourses = $user->lecturersCourses()->where('user_id', $user->id);
        $assistantCourses = $user->assistantsCourses;
        if ($lecturerCourses || $assistantCourses) {

            foreach ($lecturerCourses as $course) {
                $grade = Grade::where('course_id', $course);
                return response()->json([
                    'message' => 'Grade as lecture',
                    'grade' => $grade
                ], 200);
            }
            foreach ($assistantCourses as $course) {
                $grade = Grade::where('course_id', $course);
                return response()->json([
                    'message' => 'Grade as assistant',
                    'grade' => $grade
                ]);
            }

        }
        return response()->json([
            'message' => 'No courses found for this user as lecturer or asistante'
        ], 404);
    }
    public function updateGrades(Request $request, string $id)
    {

        $validator = Validator::make($request->all(), [
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
            return response()->json([
                'message' => 'grade not found'
            ], 404);
        }
        $user = Auth::user();
        $lectureGrade = $user->givenGrades()->where('id', $grade->id)->exists();
        if (!$lectureGrade) {
            return response()->json([
                'message' => 'Unauthorized: You did not create this grade.'
            ], 404);
        }
        $grade->points = $inputs['points'];
        $grade->save();

        return response()->json([
            'message' => 'Grade updated successfully',
            'grade' => $grade,
        ], 200);

    }

    public function showGradesForStudent()
    {
        $user = Auth::user();
        $courses = $user->studentsCourses()->where('user_id', $user->id)->get();
        if ($courses->isEmpty()) {
            return response()->json([
                'message' => 'No  courses for this user',
            ], 404);
        }
        foreach ($courses as $course) {
            $grades = Grade::where('student_id', $user->id)
                ->where('course_id', $course->id)->get();

            foreach ($grades as $grade) {
                $grade->update(['is_seen' => true]);
            }
            $data[] = [
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'code' => $course->code,
                ],
                'grades' => $grades
            ];

        }


        return response()->json([
            'message' => 'grade retrieved successfully',
            'data' => $data
        ]);

    }
    public function showGradesByCourseStudent(string $id)
    {
        $course = Course::find($id);
        $user = Auth::user();
        $isInCourse = $course->users()->where('user_id', $user->id)->exists();

        if (!$isInCourse) {
            return response()->json([
                'message' => 'User is not part of this course',
            ], 403);
        }
        $grades = Grade::where('student_id', $user->id)
            ->where('course_id', $course->id)->get();
        foreach ($grades as $grade) {
            $grade->update(['is_seen', true]);
        }
        $data[] = [
            'course' => [
                'name' => $course->name,
                'code' => $course->code,
            ],
            'grades' => $grades
        ];
        return response()->json([
            'message' => 'Grades retrieved successfully',
            'grades' => $data
        ]);
    }
}
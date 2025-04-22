<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Course;
use App\Models\Degree;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserCourse extends Controller
{
    public function getCoursesByDegree(string $degreeName)
    {
        $degree = Degree::where('name', $degreeName)->first();
        if (!$degree) {
            return response()->json([
                'message' => 'Degree not found',
            ], 404);
        }
        $courses = Course::where('degree_id', $degree->id)->get();

        if ($courses->isEmpty()) {
            return response()->json([
                'message' => 'No courses found for this degree',
            ], 404);
        }
        return response()->json([
            'message' => 'Courses found',
            'data' => $courses,
        ], 200);
    }

    public function getCoursesByCategory($category)
    {
        $courses = Course::where('category', $category)->get();
        if ($courses->isEmpty()) {
            return response()->json([
                'message' => 'No courses found for this category',
            ], 404);
        }
        return response()->json([
            'message' => 'Courses found',
            'data' => $courses,
        ], 200);
    }

    public function getCoursesBySemester($semester)
    {
        $courses = Course::where('semester', $semester)->get();
        if ($courses->isEmpty()) {
            return response()->json([
                'message' => 'No courses found for this semester',
            ], 404);
        }
        return response()->json([
            'message' => 'Courses found',
            'data' => $courses,
        ], 200);
    }

    public function asignCourseToUser(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'institution_number' => 'required|string|max:8',
            'course_name' => 'required|string',
            'role' => 'required|in:lecturer,assistant,student',
            'status' => 'nullable|string',
            'class_group_name' => 'nullable|string'
        ]);
    
        if ($validators->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validators->errors(),
            ], 422);
        }
    
        $inputs = $validators->validated();
        $user = User::where('institution_number', $inputs['institution_number'])->first();
        $course = Course::where('name', $inputs['course_name'])->first();
        $year = Carbon::now()->year;
    
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    
        if ($user->role->name === 'lecturer' && ($inputs['role'] === 'lecturer' || $inputs['role'] === 'assistant')) {
            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }
    
            if ($course->users()->where('user_id', $user->id)->where('academic_year', $year)->exists()) {
                return response()->json([
                    'message' => 'User already assigned to this course',
                ], 409);
            }
    
            $course->users()->attach($user->id, [
                'role' => $inputs['role'],
                'academic_year' => $year,
                'is_active' => true,
            ]);
    
            return response()->json([
                'message' => 'Course assigned to user successfully',
            ], 200);
        }
    
        if ($inputs['role'] === 'student') {
            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }
        
            // Check duplicate course assignment for the academic year
            if ($course->users()->where('user_id', $user->id)->where('academic_year', $year)->exists()) {
                return response()->json([
                    'message' => 'User already assigned to this course',
                ], 409);
            }
        
            // Calculate student's current academic year (1st, 2nd, or 3rd)
            $currentYear = date('Y');
            $currentMonth = date('n');
            $academicYear = $currentMonth >= 9 ? $currentYear : $currentYear - 1;
            $studentYear = $academicYear - $user->year_started + 1;
        
            // Check the course semester group based on the current time
            $currentSemesterGroup = ($currentMonth >= 9 || $currentMonth <= 1) ? 1 : 2;
            $semesterGroups = [
                1 => [1, 3, 5], // Odd semesters (typically Fall)
                2 => [2, 4, 6], // Even semesters (typically Spring)
            ];
        
            if (!in_array($course->semester, $semesterGroups[$currentSemesterGroup])) {
                return response()->json([
                    'message' => 'Course does not belong to the current semester',
                ], 403);
            }
        
            // Determine the academic year of the course from the semester number
            $courseYear = match ($course->semester) {
                1, 2 => 1,
                3, 4 => 2,
                5, 6 => 3,
            };
        
            // Validate that the course is not from a future academic year
            if ($studentYear < $courseYear) {
                return response()->json([
                    'message' => 'Student cannot be assigned to a course from a future academic year',
                ], 403);
            }
        
            // Continue attaching the course to the student
            $attachData = [
                'role' => $inputs['role'],
                'academic_year' => $year,
                'status' => $inputs['status'] ?? null,
                'is_active' => true,
            ];
        
            if (isset($inputs['class_group_name'])) {
                $classGroup = ClassGroup::where('name', $inputs['class_group_name'])->first();
                $yearStudy = Carbon::now()->year - $user->year_started;

                if ($classGroup) {
                    if($user->degree_id===$classGroup->degree_id && $yearStudy >= $classGroup->year_study){
                        $attachData['class_group_id'] = $classGroup->id;
                    }else{
                        return response()->json([
                            'message'=>"you cannot assing to $classGroup->name because the student is in $yearStudy year"
                        ]);
                    }
                }
            }
        
            $course->users()->attach($user->id, $attachData);
        
            return response()->json([
                'message' => 'Course assigned to user successfully',
            ], 200);
        }
    
        return response()->json([
            'message' => 'This user cannot be on this role'
        ]);
    }
    

    public function updateUserCourse(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'institution_number' => 'required|string|max:8',
            'course' => 'required|string|max:255',
            'role' => 'nullable|in:lecturer,assistant,student',
            'status' => 'nullable|string|max:255',
            'academic_year' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'class_group_name' => 'nullable|string'

        ]);

        if ($validators->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validators->errors(),
            ], 422);
        }

        $inputs = $validators->validated();
        $user = User::where('institution_number', $inputs['institution_number'])->first();
        $course = Course::where('name', $inputs['course'])->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($user->role->name === 'lecturer' && ($inputs['role'] === 'lecturer' || $inputs['role'] === 'assistant')) {
            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            if ($course->users()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'message' => 'User already assigned to this course',
                ], 409);
            }
            if ($inputs['role'] === 'student') {
                $course->users()->attach($user->id, [
                    'role' => $inputs['role'],
                    'status' => $inputs['status'] ?? 'enrolled'
                ]);
            }

            $course->users()->attach($user->id, [
                'role' => $inputs['role'],
            ]);

            return response()->json([
                'message' => 'Course assigned to user successfully',
            ], 200);
        }

        $userCourse = $course->users()->where('user_id', $user->id)->first();

        if (!$userCourse) {
            return response()->json([
                'message' => 'User is not assigned to this course',
            ], 404);
        }

        $updateData = [];
        if (isset($inputs['role'])) {
            $updateData['role'] = $inputs['role'];
        }
        if (isset($inputs['status'])) {
            $updateData['status'] = $inputs['status'];
        }
        if (isset($inputs['academic_year'])) {
            $updateData['academic_year'] = $inputs['academic_year'];
        }
        if (isset($inputs['is_active'])) {
            $updateData['is_active'] = $inputs['is_active'];
        }
        if (isset($inputs['class_group_name'])) {
            $classGroup = ClassGroup::where('name', $inputs['class_group_name'])->first();
            $updateData['class_group_id'] = $classGroup->id;
        }

        $course->users()->updateExistingPivot($user->id, $updateData);

        return response()->json([
            'message' => 'User course updated successfully',
        ], 200);
    }
}

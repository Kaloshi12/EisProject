<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function recordAttendanceByLecturer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'students' => 'required|array',
            'students.*.name' => 'required|string',
            'students.*.surname' => 'required|string',
            'students.*.hours_attended' => 'required|integer',
            'course_name' => 'required|string',
            'topic' => 'required|string',
            'category' => 'required|in:lesson,lab,seminar',
            'week' => 'required|integer',
            'number_hours' => 'required|integer',
            'date' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inputs = $validator->validated();
        $lecturer = Auth::user();

        $course = Course::where('name', $inputs['course_name'])->first();
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $year = Carbon::parse($inputs['date'])->year;
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

        foreach ($inputs['students'] as $studentData) {
            $student = User::where('name', $studentData['name'])
                ->where('surname', $studentData['surname'])
                ->first();

            if (!$student) {
                continue;
            }

            $isEnrolled = DB::table('course_user')
                ->where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('role', 'student')
                ->where('academic_year', $year)
                ->where('status', 'enrolled')
                ->exists();

            if (!$isEnrolled) {
                continue;
            }

            Attendance::create([
                'lecturer_id' => $lecturer->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'topic' => $inputs['topic'],
                'category' => $inputs['category'],
                'week' => $inputs['week'],
                'date' => $inputs['date'],
                'hours_attended' => $studentData['hours_attended'],
                'number_hours' => $inputs['number_hours'],
            ]);
        }

        return response()->json(['message' => 'Attendance recorded successfully']);
    }
    public function updateAttendanceByLecturer(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'hours_attended' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inputs = $validator->validated();
        $lecturer = Auth::user();

        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        $course = Course::find($attendance->course_id);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $isLecturer = $lecturer->lecturersCourses()->where('course_id', $course->id)->exists();
        $isAssistant = $lecturer->assistantsCourses()->where('course_id', $course->id)->exists();

        if (!$isLecturer) {
            if (!$isAssistant) {
                return response()->json(['message' => 'Unauthorized to update attendance for this course'], 403);
            }
        }
        if (isset($inputs['hours_attended'])) {
            $attendance->hours_attended = $inputs['hours_attended'];
            $attendance->save();
            return response()->json(['message' => 'Attendance updated successfully']);
        }

    }


    public function getAttendanceSummaryPerCourse()
    {
        $student = Auth::user();

        $attendances = Attendance::with('course')
            ->where('student_id', $student->id)
            ->orderBy('date')
            ->get();

        $groupedAttendances = $attendances->groupBy('course_id');
        $result = [];


        foreach ($groupedAttendances as $courseId => $courseAttendances) {
            $courseName = Course::find($courseId)->name;
            $courseCode = Course::find($courseId)->code;
            $courseSemester = Course::find($courseId)->semester;
            $totalHours = $totalAttended = 0;
            $lectureHours = $lectureAttended = 0;
            $seminarHours = $seminarAttended = 0;
            $sessions = [];

            foreach ($courseAttendances as $record) {
                $session = [
                    'week' => $record->week,
                    'topic' => $record->topic,
                    'type' => $record->category,
                    'scheduled_hours' => $record->number_hours,
                    'attended_hours' => $record->hours_attended,
                    'date' => $record->date,
                ];
                $totalHours += $record->number_hours;
                $totalAttended += $record->hours_attended;
                if ($record->category === 'lesson') {
                    $lectureHours += $record->number_hours;
                    $lectureAttended += $record->hours_attended;
                } elseif (in_array($record->category, ['lab', 'seminar'])) {
                    $seminarHours += $record->number_hours;
                    $seminarAttended += $record->hours_attended;
                }
            }
            $sessions[] = $session;

            $lecturePercent = $lectureHours ? round(($lectureAttended / $lectureHours) * 100, 2) : 0;
            $seminarPercent = $seminarHours ? round(($seminarAttended / $seminarHours) * 100, 2) : 0;
            $totalPercent = $totalHours ? round(($totalAttended / $totalHours) * 100, 2) : 0;

            $result[] = [
                'course_id' => $courseId,
                'course_name' => $courseName,
                'course_code' => $courseCode,
                'course_semester' => $courseSemester,
                'lecture_attendance_percent' => $lecturePercent,
                'seminar_attendance_percent' => $seminarPercent,
                'total_attendance_percent' => $totalPercent,
                'sessions' => $sessions
            ];
        }

        return response()->json($result);
    }
    public function getAttendanceForGivenCourse(string $id)
    {
        $student = Auth::user();

        $attendances = Attendance::with('course')
            ->where('student_id', $student->id)
            ->where('course_id', $id)
            ->orderBy('date')
            ->get();


        $course = Course::find($id);
        $sessions = [];
        $totalHours = $totalAttended = 0;
        $lectureHours = $lectureAttended = 0;
        $seminarHours = $seminarAttended = 0;

        foreach ($attendances as $record) {
            $sessions[] = [
                'week' => $record->week,
                'topic' => $record->topic,
                'type' => $record->category,
                'scheduled_hours' => $record->number_hours,
                'attended_hours' => $record->hours_attended,
                'date' => $record->date->format('D, d M Y'),
            ];
            $totalHours += $record->number_hours;
            $totalAttended += $record->hours_attended;
            if ($record->category === 'lesson') {
                $lectureHours += $record->number_hours;
                $lectureAttended += $record->hours_attended;
            } elseif (in_array($record->category, ['lab', 'seminar'])) {
                $seminarHours += $record->number_hours;
                $seminarAttended += $record->hours_attended;
            }
        }

        $lecturePercent = $lectureHours ? round(($lectureAttended / $lectureHours) * 100, 2) : 0;
        $seminarPercent = $seminarHours ? round(($seminarAttended / $seminarHours) * 100, 2) : 0;
        $totalPercent = $totalHours ? round(($totalAttended / $totalHours) * 100, 2) : 0;

        $result = [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'course_code' => $course->code,
            'course_semester' => $course->semester,
            'lecture_attendance_percent' => $lecturePercent,
            'seminar_attendance_percent' => $seminarPercent,
            'total_attendance_percent' => $totalPercent,
            'sessions' => $sessions,
        ];

        return response()->json($result);
    }

}

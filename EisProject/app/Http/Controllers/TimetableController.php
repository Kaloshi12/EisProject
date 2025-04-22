<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use App\Models\Course;
use App\Models\CourseHours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class TimetableControll extends Controller
{
    public function studentTimetable()
    {
        $student = Auth::user();
        $courses = $student->studentsCourses()->where('user_id', $student->id)->where('status', 'enrolled')->where('is_active', true)->get();
        $timetable = [];
        foreach ($courses as $course) {
            $courseHours = CourseHours::where('course_id', $course->id)->get();
            foreach ($courseHours as $courseHour) {
                $start = Carbon::createFromFormat('H:i:s', $courseHour->start_hour);
                $end = $start->copy()->addHours($courseHour->num_hours);
                $classGroup = ClassGroup::find($courseHour->class_group_id);
                $timetable[] = [
                    'course_name' => $course->name,
                    'course_code' => $course->code,
                    'class_group' => $classGroup->name,
                    'day' => $courseHour->day,
                    'category' => $courseHour->category,
                    'start_hour' => $start->format('H:i'),
                    'end_hour' => $end->format('H:i')
                ];
            }
        }
        return response()->json(['timetable' => $timetable]);
    }
    public function publicTimetable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_group_name' => 'required|string'
        ]);
        $inpupts = $validator->validated();
        $timetable = [];
        $classGroup = ClassGroup::where('name', $inpupts['class_group_name'])->first();
        $timetableRecord = CourseHours::where('class_group_id', $classGroup->id)->get();
        foreach ($timetableRecord as $courseHour) {
            $start = Carbon::createFromFormat('H:i:s', $courseHour->start_hour);
            $end = $start->copy()->addHours($courseHour->num_hours);
            $course = Course::find($courseHour->course_id);
            $timetable[] = [
                'course_name' => $course->name,
                'day' => $courseHour->day,
                'category' => $courseHour->category,
                'start_hour' => $start->format('H:i'),
                'end_hour' => $end->format('H:i')
            ];

        }
        return response()->json([
            'class_group' => $classGroup->name,
            'timetable' => $timetable
        ]);
    }
    public function lecturerTimetable()
    {
        $lecturer = Auth::user();
        $courses = $lecturer->lecturerCourses()->where('user_id', $lecturer->id)->where('is_active', true)->get();
        $timetable = [];
        foreach ($courses as $course) {
            $courseHours = CourseHours::where('course_id', $course->id)->get();
            foreach ($courseHours as $courseHour) {
                $start = Carbon::createFromFormat('H:i:s', $courseHour->start_hour);
                $end = $start->copy()->addHours($courseHour->num_hours);
                $classGroup = ClassGroup::find($courseHour->class_group_id);
                $timetable[] = [
                    'course_name' => $course->name,
                    'class_group' => $classGroup->name,
                    'day' => $courseHour->day,
                    'category' => $courseHour->category,
                    'start_hour' => $start->format('H:i'),
                    'end_hour' => $end->format('H:i')
                ];
            }
        }
        return response()->json(['timetable' => $timetable]);
    }
}

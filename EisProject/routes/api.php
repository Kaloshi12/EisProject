<?php

use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminGradeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseHoursController;
use App\Http\Controllers\DegreesController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\FacultiesController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentPaymentCheckController;
use App\Http\Controllers\TimetableControll;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCourse;
use App\Http\Middleware\AdminRoleMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\LecturerMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\VerifyEmailMiddleware;



Route::post('login', [AuthController::class, 'login']);

Route::middleware(AuthMiddleware::class)->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('courses-by-degree/{degreeName}', [UserCourse::class, 'getCoursesByDegree']);
    Route::get('courses-by-category/{category}', [UserCourse::class, 'getCoursesByCategory']);
    Route::get('courses-by-semester/{semester}', [UserCourse::class, 'getCoursesBySemester']);

});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('verify-email', [EmailVerifyController::class, 'verifyUserEmail']);
Route::post('set-password', [EmailVerifyController::class, 'setPassword'])->middleware(VerifyEmailMiddleware::class);
Route::get('public-timetable', [TimetableControll::class, 'publicTimetable']);


Route::middleware([AuthMiddleware::class, LecturerMiddleware::class])->prefix('lecturer')->group(function () {
    Route::post('grades', [GradeController::class, 'publishGrade']);
    Route::put('grades/{$id}', [GradeController::class, 'updateGrades']);
    Route::get('grades', [GradeController::class, 'seeGradesByLecture']);
    Route::post('attendance', [AttendanceController::class, 'recordAttendanceByLecturer']);
    Route::put('attendance/{id}', [AttendanceController::class, 'updateAttendanceByLecturer']);
    Route::get('timetable', [TimetableControll::class, 'lecturerTimetable']);

});
Route::middleware([AuthMiddleware::class, ])->prefix('student')->group(function () {
    Route::get('grades', [GradeController::class, 'showGradesForStudent']);
    Route::get('attendance', [AttendanceController::class, 'getAttendanceSummaryPerCourse']);
    Route::get('attendance/{$id}', [AttendanceController::class, 'getAttendanceForGivenCourse']);
    Route::get('timetable', [TimetableControll::class, 'studentTimetable']);
    Route::get('show-debit', [StudentPaymentCheckController::class, 'debit']);
    Route::get('payments-records', [StudentPaymentCheckController::class, 'getPaymentsRecord']);
    Route::post('request-documents', [DocumentRequestController::class, 'makeDucumentRequest']);
    Route::get('penging-documents', [DocumentRequestController::class, 'studentPendingDocumentRequest']);
    Route::get('document-history', [DocumentRequestController::class, 'showPendingDocumentRequest']);


});
Route::middleware([AdminMiddleware::class, AuthMiddleware::class])->group(function () {
    Route::post('store-user', [UserController::class, 'store']);
    Route::post('delete-user', [UserController::class, 'deleteUser']);
    Route::apiResource('faculties', FacultiesController::class);
    Route::apiResource('departments', DepartmentsController::class);
    Route::apiResource('degrees', DegreesController::class);
    Route::apiResource('class-group', ClassGroupController::class);
    Route::apiResource('classroom', ClassroomController::class);
    Route::apiResource('course', CourseController::class);
    Route::apiResource('grade', AdminGradeController::class);
    Route::post('add-course-to-user', [UserCourse::class, 'asignCourseToUser']);
    Route::post('update-user-course', [UserCourse::class, 'updateUserCourse']);
    Route::apiResource('attendance', AdminAttendanceController::class);
    Route::apiResource('timatable', CourseHoursController::class);
    Route::apiResource('payment', PaymentController::class);
    Route::apiResource('document', DocumentsController::class);
    Route::get('pending-document-request', [DocumentRequestController::class, 'showPendingDocumentRequest']);
    Route::get('document-request/{$status}', [DocumentRequestController::class, ' getDocumentRequestsByStatus']);
    Route::post('/admin/document-request/{id}/approve', [DocumentRequestController::class, 'approveDocumentRequest']);
    Route::post('pay-document/{$id}', [DocumentRequestController::class, 'payDocumentRequest']);

});


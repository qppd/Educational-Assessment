<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ReviewerController;


Route::get('/admin', function () {
    if (session()->has('administrator')) {
        return redirect('/admin/home');
    }
    return view('auth.login');
});


Route::get('/admin/login', function () {
    if (session()->has('administrator')) {
        return redirect('/admin/home');
    }
    return view('auth.login');
});

Route::post("/admin/login", [LoginController::class, 'administratorLogin'])->middleware('throttle:10,1');

// Login Route


// Logout Route
Route::get('/admin/logout', function () {
    if (session()->has('administrator')) {
        session()->pull('administrator');
    }
    return redirect('/admin');
});
// Logout Route


Route::post('/admin/account/password/update', [LoginController::class, 'administratorupdatePassword']);


// Home Route
Route::get("/admin/home", [HomeController::class, 'fetchHome']);
// Home Route



// Administrators Route
Route::get("/admin/administrators", [AdministratorController::class, 'fetchAdministrators']);
Route::post("/admin/administrator/add", [AdministratorController::class, 'addAdministrator']);
Route::post("/admin/administrator/edit", [AdministratorController::class, 'editAdministrator']);
Route::post("/admin/administrator/delete", [AdministratorController::class, 'removeAdministrator']);
// Administrators Route



// Students Route
Route::get("/admin/students", [StudentController::class, 'fetchStudents']);
Route::post("/admin/student/add", [StudentController::class, 'addStudent']);
Route::post("/admin/student/edit", [StudentController::class, 'editStudent']);
Route::post("/admin/student/editaccount", [StudentController::class, 'editStudent']);
Route::get("/admin/student/delete", [StudentController::class, 'removeStudent']);
Route::post("/admin/student/upload", [StudentController::class, 'uploadStudents']);
Route::get("/admin/student/export", [StudentController::class, 'exportTemplate']);
Route::get('/admin/student/images/{studentNo}', [StudentController::class, 'getStudentImages']);
// Route::get('/admin/student/images/{studentNo}/{image}', [StudentController::class, 'getStudentImage']);
// Students Route


// Professors Route
Route::get("/admin/professors", [ProfessorController::class, 'fetchProfessors']);
Route::post("/admin/professors/add", [ProfessorController::class, 'addProfessor']);
Route::post("/admin/professors/edit", [ProfessorController::class, 'editProfessor']);
Route::get("/admin/professors/delete", [ProfessorController::class, 'removeProfessor']);
Route::post("/admin/professor/upload", [ProfessorController::class, 'uploadProfessors']);
Route::get("/admin/professor/export", [ProfessorController::class, 'exportTemplate']);
// Professors Route

// Examinations Route
//Route::view("/admin/examinations", 'examinations');
Route::get("/admin/examinations", [ExaminationController::class, 'fetchExaminations']);
Route::post("/admin/examinations/add", [ExaminationController::class, 'addExamination']);
Route::post("/admin/examinations/edit", [ExaminationController::class, 'editExamination']);
Route::post("/admin/examinations/delete", [ExaminationController::class, 'removeExamination']);
Route::get("/admin/examinations/manage/{id}", [ExaminationController::class, 'manageExamination']);
Route::get("/admin/examinations/reviewers/{id}", [ExaminationController::class, 'manageReviewers']);
Route::get('/admin/examinations/reviewer/download/{file}', [ReviewerController::class, 'download'])->name('download');
Route::post("/admin/examinations/reviewer/approve", [ReviewerController::class, 'approveReviewer']);
Route::post("/admin/examinations/reviewer/reject", [ReviewerController::class, 'rejectReviewer']);
Route::post("/admin/examinations/reviewer/upload", [ReviewerController::class, 'uploadAdminReviewer']);
Route::get("/admin/examinations", [ExaminationController::class, 'fetchExaminations']);
Route::post("/admin/examinations/questions/add", [QuestionController::class, 'addQuestion']);
Route::post("/admin/examinations/manage/approve", [QuestionController::class, 'approveQuestion']);
Route::post("/admin/examinations/manage/reject", [QuestionController::class, 'rejectQuestion']);

// Examinations Route


//=============================================================================================================

// Faculty Route
Route::post('/faculty/account/password/update', [FacultyController::class, 'facultyupdatePassword']);

Route::get("/faculty/dash", [FacultyController::class, 'fetchDash']);
Route::get("/faculty/examinations", [FacultyController::class, 'fetchExaminations']);
Route::get("/faculty/examinations/manage/{id}", [FacultyController::class, 'fetchQuestions']);
Route::post("/faculty/examinations/manage/request", [FacultyController::class, 'requestQuestion']);
Route::post("/faculty/examinations/manage/request/edit", [FacultyController::class, 'editQuestion']);
Route::post("/faculty/examinations/manage/upload", [ReviewerController::class, 'uploadReviewer']);
//Route::post("/admin/examinations/questions/add", [QuestionController::class, 'addProfQuestion']);
//addProfQuestion
// Faculty Route

Route::get('/faculty', function () {
    if (session()->has('faculty')) {
        return redirect('/faculty/dash');
    }
    return view('faculty.login');
});


Route::get('/faculty/login', function () {
    if (session()->has('faculty')) {
        return redirect('/faculty/dash');
    }
    return view('faculty.login');
});

Route::post("/faculty/login", [FacultyController::class, 'facultyLogin'])->middleware('throttle:10,1');

// Login Route


// Logout Route
Route::get('/faculty/logout', function () {
    if (session()->has('faculty')) {
        session()->pull('faculty');
    }
    return redirect('/faculty');
});
// Logout Route




//=============================================================================================================


// Student Portal Route
Route::get("/", [PortalController::class, 'portals']);
Route::get("/portal", [PortalController::class, 'portals']);
Route::get("/portal/forgot", [PortalController::class, 'forgotPassword']);
Route::post("/portal/login", [PortalController::class, 'studentLogin'])->middleware('throttle:10,1');
Route::get("/portal/register", [PortalController::class, 'registerPage']);
Route::post("/portal/register/add", [PortalController::class, 'studentRegister']);
Route::post("/portal/forgot/password/update", [PortalController::class, 'saveNewPassword']);

Route::get("/portal/dashboard", [PortalController::class, 'dashboardPage']);
Route::get("/portal/examinations/available", [PortalController::class, 'examinationsAvailablePage']);
Route::get('/portal/examinations/available/download/{examination_id}', [ReviewerController::class, 'downloadReviewers'])->name('downloadReviewers');
Route::get("/portal/examinations/taken", [PortalController::class, 'examinationsTakenPage']);
//Route::get("/portal/examination/attempt/{examination}", [PortalController::class, 'examinationAttempt']);
Route::post("/portal/examinations/attempt/", [PortalController::class, 'examinationAttempt']);
Route::get("/portal/examinations/result/{examination_id}", [PortalController::class, 'examinationResult']);
Route::post("/portal/examination/submit", [PortalController::class, 'examinationSubmit']);
Route::get('/find-user/{label}', [PortalController::class, 'findUserByLabel']);


Route::get('/portal/logout', function () {
    if (session()->has('student')) {
        session()->pull('student');
    }
    return redirect('/portal');
});
// Student Portal Route
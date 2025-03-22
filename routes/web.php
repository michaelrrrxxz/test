<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    UserController,
    CourseController,
    DepartmentController,
    ProfileController,
    AuthController,
    BatchController,
    EnrolledStudentController,
    StudentController,
    InstructorController,
    QuestionController,
    ExamController,
    DashboardController,
    SchoolController,
    LocationController,
    CKEditorController,
    ResultController,
    InformationController,
    ExampleController,
    OTPController,
    InstructorDashboardController,
};

use App\Http\Controllers\IPController;

Route::get('/get-ip', [IPController::class, 'getClientIP']);



Route::controller(ProfileController::class)->prefix('Profile')->group(function () {
    Route::get('/getProfile', 'getProfile')->name('Profile/getProfile');
    Route::post('/update', 'update')->name('Profile/update');
    Route::post('/delete', 'destroy')->name('Profile/delete');
  
});


Route::get('/clear-session', function () {
    Session::forget('studentId'); // Clears only the 'studentId' session variable
    return redirect('/'); // Redirects to the home route
})->name('clear.session');


    //OTP
    Route::controller(OTPController::class)->group(function () {
        Route::post('/check-otp',  'verifyOtp')->name('check.otp');
        Route::post('/send-otp',  'sendOtp')->name('send.otp');
    }); 
    

Route::get('Exam/info', function () {
    return view('layouts.information');
});




Route::controller(SchoolController::class)->group(function () {
    Route::get('Exam/Exam/getSchoolsname', 'getSchoolsname')->name('Exam/Exam/getSchoolsname');
});
Route::controller(EnrolledStudentController::class)->group(function () {
    Route::post('EnrolledStudents/verifyAccessKey', 'verifyAccessKey')->name('EnrolledStudents/verifyAccessKey');
    Route::get('NoEmail', 'NoEmail')->name('EnrolledStudents/NoEmail');
});


// Route to display the information form
Route::get('Exam/information-form', [ExamController::class, 'showInformationForm'])->name('Exam/information-form');
Route::get('Exam/testing/{student_id}/{course}', [ExamController::class, 'show'])->name('exam.test');

Route::get('/123', function () {
    return view('layouts.information');
});


Route::get('/export-pdf', [ResultController::class, 'exportPdf'])->name('export.pdf');



// Route to handle form submission and save the data
Route::post('Exam/information-form', [ExamController::class, 'storeInformation']);



Route::get('mama',  function(){
    return view("Exam.example");
});

Route::get('Try', [ExamController::class, 'index'])->name('Try');

Route::get('time', function () {
    echo date_default_timezone_get();
});

Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');

Route::get('/verify-otp', function () {
    return view('Exam.verify');

})->name('verify.otp');


// Route::get('login/user', [AuthController::class, 'showLoginForm'])->name('user.login');
// Route::post('login/user', [AuthController::class, 'login']);
// Route::post('logout/user', [AuthController::class, 'logout'])->name('user.logout');

// Route::get('login/instructor', [InstrAuthController::class, 'showLoginForm'])->name('instructor.login');
// Route::post('login/instructor', [InstructorAuthController::class, 'login']);
// Route::post('logout/instructor', [InstructorAuthController::class, 'logout'])->name('instructor.logout');

Route::get('/hello', function () {
    return view('emails.otp');
});







Route::middleware('guest')->group(function () {

    
    
    // Route::get('/', function () {
    //     return view('root.index')
    //     Route::post('Logout', 'logout')->name('Logout');
    // })->name('home');
    
    Route::controller(AuthController::class)->group(function () {
        Route::post('Login', 'login')->name('Login');
        Route::post('Login/Instructors', 'instructorLogin')->name('Login/Instructors');
        Route::get('Admin/login', 'admin')->name('Admin/login');
        Route::get('Instructor/login', 'hello')->name('Instructor/login');
        Route::get('/', 'root')->name('/');
    });
});


Route::controller(AuthController::class)->group(function(){
    Route::post('Logout', 'logout')->name('Logout');
    
});


Route::get('/students/no-exam/pdf', [EnrolledStudentController::class, 'generatePdf'])->name('students.noexam.pdf');










    


    Route::get('/profile', [AuthController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'update'])->name('profile.update');




    Route::controller(AuthController::class)->group(function(){
       
        Route::get('getLoginHistory', 'getLoginHistory')->name('getLoginHistory');
        Route::get('viewLoginHistory', 'viewLoginHistory')->name('viewLoginHistory');
    });
    


    Route::get('/enter-email', function () {
        return view('enter-email');
    });



    Route::controller(InformationController::class)->group(function () {
        Route::get('Information/index', 'index')->name('Information/index');
    });
       
    
 
  
    Route::controller(ExampleController::class)->group(function () {                                  
        Route::get('Example', 'index')->name('Example');
    });
    



    

    

    Route::controller(SchoolController::class)->group(function () {
        Route::get('Schools', 'index')->name('Schools');
        Route::get('Schools/getSchools', 'getSchools')->name('Schools/getSchools');
        Route::get('Schools/edit/{id}', 'edit')->name('Schools/edit');
        Route::post('Schools/add', 'store')->name('Schools/add');
        Route::post('Schools/update/{id}', 'update')->name('Schools/update');
        Route::post('Schools/delete/{id}', 'destroy')->name('Schools/delete');
    }); 
    Route::controller(NLPController::class)->group(function () {
      
        Route::get('Nlp/getData', 'getResults')->name('Nlp/getData');
        Route::get('Nlp/getNLPSuggestions', 'getNLPSuggestions')->name('Nlp/getNLPSuggestions');
       
    }); 
    
    
    Route::controller(InstructorController::class)->group(function () {
        Route::get('Instructors', 'index')->name('Instructors');
        Route::get('Instructors/getInstructors', 'getInstructors')->name('Instructors/getInstructors');
        Route::post('Instructors/add', 'store')->name('Instuctors/add');
        Route::post('Instructors/delete/{id}', 'destroy')->name('Instructors/delete');
        Route::get('Instructors/edit/{id}', 'edit')->name('Instructors/edit');
        Route::post('Instructors/update/{id}', 'update')->name('Instructors/update');
    }); 

   



Route::controller(InformationController::class)->group(function(){
    Route::post('Information/add', 'store')->name('Information/add');
 
    
});


Route::get('Exam/Exam/regions', [LocationController::class, 'index']);
Route::get('/regions', [LocationController::class, 'index']);
Route::post('/questions/store', [QuestionController::class, 'store'])->name('questions.store');
Route::post('/questions/upload-image', [QuestionController::class, 'uploadImage'])->name('questions.uploadImage');
Route::post('/assign-batch/{enrolledStudentId}/{batchId}', [StudentController::class, 'assignBatch']);
// Assign all enrolled students to the specified batch with one click
Route::post('/assign-all-to-batch/{id}', [StudentController::class, 'assignAllToBatch']);





Route::post('/admin-authenticate', [OTPController::class, 'authenticateAdmin'])->name('authenticateAdmin');



Route::prefix('Instructors')->middleware(['auth'])->group(function () {
    Route::controller(InstructorDashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('Instructors/dashboard');
        Route::get('/getCategory', 'getCategory')->name('Instructors/getCategory');
        // Routes to get data for Instructor Dashboard
        Route::get('/exam-year-data', 'getExamYearData')->name('InstructorDashboard.examYearData');
        Route::get('/logs', 'getLogs')->name('InstructorDashboard.logs');
        Route::get('/exam-participation', 'getExamParticipationData')->name('InstructorDashboard.examParticipationData');
        Route::get('/countEnrolledStudentsbyGender', 'countEnrolledStudentsbyGender')->name('InstructorDashboard.genderCount');
    });
});


//admin
Route::middleware(['auth','no-cache','role:administrator'])->group(function () {
    Route::middleware(['auth','no-cache'])->group(function () {
        Route::controller(DashboardController::class)->prefix('Dashboard')->group(function () {
            Route::get('/', 'index')->name('Dashboard');
            Route::get('/getCategory', 'getCategory')->name('Dashboard/getCategory');
            Route::get('/countStudentsbyCourse', 'countStudentsbyCourse')->name('countStudentsbyCourse');
            Route::get('/getExamParticipationData', 'getExamParticipationData')->name('Dashboard/getExamParticipationData');
            Route::get('/getExamYearData', 'getExamYearData')->name('Dashboard/getExamYearData');
            Route::get('/countEnrolledStudents', 'countEnrolledStudents')->name('EnrolledStudents/countEnrolledStudents');
            Route::get('/countEnrolledStudentsbyGender', 'countEnrolledStudentsbyGender')->name('Dashboard/countEnrolledStudentsbyGender');
            Route::get('/getLogs', 'getLogs')->name('Dashboard/getLogs');
         });
    //users
    Route::controller(UserController::class)->prefix('Users')->group(function () {
        Route::get('/', 'index')->name('Users');
        Route::get('/getUsers', 'getUsers')->name('Users/getUsers');
        Route::get('/getInstructors', 'getInstructors')->name('Users/getInstructors');
        Route::get('/getUser/{id}', 'show')->name('Users/getUser');
        Route::get('/edit/{id}', 'edit')->name('Users/edit');
        Route::post('/add', 'store')->name('Users/add');
        Route::post('/update/{id}', 'update')->name('Users/update');
        Route::post('/delete/{id}', 'destroy')->name('Users/delete');
        Route::post('/verify-otp', 'verifyOtp')->name('Users/verifyOtp');
    });
    Route::controller(CourseController::class)->prefix('Courses')->group(function () {
        Route::get('/', 'index')->name('Courses');
        Route::get('/getCourses', 'getCourses')->name('Course/getCourses');
        Route::get('/edit/{id}', 'edit')->name('Course/edit');
        Route::post('/add', 'store')->name('Course/add');
        Route::post('/update/{id}', 'update')->name('Course/update');
        Route::post('/delete/{id}', 'destroy')->name('Course/delete');
    });
    Route::controller(DepartmentController::class)->prefix('Departments')->group(function () {
        Route::get('/', 'index')->name('Departments');
        Route::get('/getDepartments', 'getDepartments')->name('Departments/getCourses');
        Route::get('/edit/{id}', 'edit')->name('Departments/edit');
        Route::post('/add', 'store')->name('Departments/add');
        Route::post('/update/{id}', 'update')->name('Departments/update');
        Route::post('/delete/{id}', 'destroy')->name('Departments/delete');
    });
});
    //questions
    Route::controller(QuestionController::class)->prefix('Questions')->group(function () {
        Route::get('/', 'index')->name('Questions');
        Route::get('/check-total-questions', 'checkTotalQuestions')->name('check');

        Route::get('/getQuestions', 'getQuestions')->name('Questions/getQuestions');
        Route::get('/getVerbalQuestions', 'getVerbalQuestions')->name('Questions/getVerbalQuestions');
        Route::get('/getNonVerbalQuestions', 'getNonVerbalQuestions')->name('Questions/getNonVerbalQuestions');
        Route::get('/edit/{id}', 'edit')->name('Questions/edit');
        Route::get('/show/{id}', 'show')->name('Questions/show');
        Route::post('/add', 'store')->name('Questions/add');
        Route::post('/update/{id}', 'update')->name('Questions/update');
        Route::post('/delete/{id}', 'destroy')->name('Questions/delete');
    });
    //batch
    Route::controller(BatchController::class)->prefix('Batch')->group(function () {
        Route::get('/', 'index')->name('Batch');
        Route::get('/checkActiveBatch', 'checkActiveBatch')->name('Batch/checkActiveBatch');
        Route::get('/getBatch', 'getBatch')->name('Batch/getBatch');
        Route::get('/getviewBatch', 'getviewBatch')->name('Batch/viewgetBatch');
        Route::get('/show', 'show')->name('Batch/show');
        Route::get('/edit/{id}', 'edit')->name('Batch/edit');
        Route::get('/getStudentsByBatch/{batchId}', 'getStudentsByBatch')->name('Batch/getStudentsByBatch');
        Route::get('/printall', 'printall')->name('Batch/printall');
        Route::post('/add', 'store')->name('Batch/add');
        Route::post('/update/{id}', 'update')->name('Batch/update');
    });
    //Result
    Route::controller(ResultController::class)->prefix('Results')->group(function () {                                  
        Route::get('/', 'index')->name('Results');
        Route::get('/getResults', 'getResults')->name('Results/getResults');
    }); 
    //No Exam 
    Route::controller(StudentController::class)->prefix('No-Exam')->group(function () {
        Route::get('/', 'noExam')->name('No-Exam');
        Route::get('/getnoExam', 'getnoExam')->name('Students/getnoExam');
    }); 
    //Dashboard
 

     Route::controller(EnrolledStudentController::class)->prefix('EnrolledStudents')->group(function () {
        Route::get('/', 'index')->name('EnrolledStudents');
        Route::get('/Information/EnrolledStudents/getStudentData', 'getStudentData')->name('EnrolledStudents/getStudentData');
        Route::get('/getEnrolledbyStudentsCourse', 'getEnrolledbyStudentsCourse')->name('EnrolledStudentsgetEnrolledbyStudentsCourse');
        Route::get('/getEnrolledStudents', 'getEnrolledStudents')->name('EnrolledStudents/getEnrolledStudents');
        Route::get('/edit/{id}', 'edit')->name('EnrolledStudents/edit');
        Route::post('/upload', 'upload')->name('EnrolledStudents/upload');
        Route::post('/add', 'store')->name('EnrolledStudents/add');
        Route::post('/update/{id}', 'update')->name('EnrolledStudents/update');
        Route::post('/delete/{id}', 'destroy')->name('EnrolledStudents/delete');
    }); 
});


Route::middleware('guest')->group(function () {
    Route::get('Exam', [ExamController::class, 'show'])->name('exam.show');
    Route::post('Exam/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::get('Exam/results', [ExamController::class, 'results'])->name('exam.results');
    Route::get('Exam/json', [ExamController::class, 'json'])->name('json');
    Route::get('Exam/result/{studentId}', [ResultController::class, 'getResult'])->name('exam.result');
    Route::get('Result/nlp', [ResultController::class, 'nlp']);
});
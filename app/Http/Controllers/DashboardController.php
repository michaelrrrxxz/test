<?php

namespace App\Http\Controllers;
use App\Models\{EnrolledStudent, Batch, Result,ActivityLog,Instructor,StudentAnswer,Department};
use Illuminate\Http\Request;
use App\Models\ExamAttempt;


class DashboardController extends Controller
{

    public function getExamYearData()
{
    // Get the count of students for each exam_year
    $data = EnrolledStudent::select('exam_year', \DB::raw('count(*) as count'))
                            ->groupBy('exam_year')
                            ->orderBy('exam_year') // optional, if you want to sort by year
                            ->get();

    return response()->json($data);
}

    public function getLogs() {
      
        $logs = ActivityLog::orderBy('created_at', 'desc')->limit(3)->get();
    
        return response()->json(['data' => $logs]);
    }

    public function getCategory(Request $request)
    {
        // Get department and year from the request
        $department = $request->input('department');
        $year = $request->input('year');
    
        // Fetch all results with necessary relationships, applying the filters if provided
        $results = Result::with([
                'enrolledStudent.information.region',
                'enrolledStudent.information.city',
                'enrolledStudent.information.province',
                'enrolledStudent.information.school',
                'batch'
            ])
            ->select('id', 'enrolled_student_id', 'batch_id', 'test_ip')
            ->when($department, function($query) use ($department) {
                return $query->whereHas('enrolledStudent', function($query) use ($department) {
                    $query->where('department', $department);
                });
            })
            ->when($year, function($query) use ($year) {
                return $query->whereHas('enrolledStudent', function($query) use ($year) {
                    $query->where('exam_year', $year);
                });
            })
            ->get();
    
        // Check if results are empty
        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found'], 404);
        }
    
        // Initialize counters for categories
        $belowAverageCount = 0;
        $averageCount = 0;
        $aboveAverageCount = 0;
    
        // Process all results
        foreach ($results as $result) {
            // Get the student's ID
            $studentId = $result->enrolled_student_id;
    
            // Get all answers for the student
            $studentAnswers = StudentAnswer::where('student_id', $studentId)->get();
    
            // Calculate total correct answers
            $correctAnswersCount = $studentAnswers->where('is_correct', true)->count();
    
            // Determine the category and increment the respective counter
            if ($correctAnswersCount <= 35) {
                $belowAverageCount++;
            } elseif ($correctAnswersCount <= 57) {
                $averageCount++;
            } else {
                $aboveAverageCount++;
            }
        }
    
        // Return the category counts as a JSON response
        return response()->json([
            'below_average' => $belowAverageCount,
            'average' => $averageCount,
            'above_average' => $aboveAverageCount
        ]);
    }
    
    
    

    
    
    
    public function index()
    {

 
        $departments = Department::get();
        
        $ongoingExams = ExamAttempt::with('student','results')
        ->whereNull('end_time')  // Only get ongoing exams
        ->get();

        $studentsWithoutExam = EnrolledStudent::whereDoesntHave('results')
        ->select('id_number', 'name', 'course', 'department', 'gender') 
        ->get();
        $countWithoutExam = $studentsWithoutExam->count();

        $Exam = Result::with(['enrolledStudent', 'batch']); // Load both relationships
      
        $countExam = $Exam->count();
        // $totalInstructors = Instructor::count();
        
        $batch = Batch::count();
        $enrolled = EnrolledStudent::count();
    
        return view('Admin.Dashboard.index', compact(['batch','enrolled','countWithoutExam','countExam','ongoingExams','departments']));
    }


    public function getExamParticipationData(Request $request)
    {
        // Get the list of unique exam years and sort them
        $examYears = EnrolledStudent::distinct()->pluck('exam_year')->sort();
    
        $data = [];
    
        foreach ($examYears as $year) {
            // Filter students without exam results by exam year
            $studentsWithoutExamQuery = EnrolledStudent::whereDoesntHave('results')
                ->where('exam_year', $year);
            $countWithoutExam = $studentsWithoutExamQuery->count();
    
            // Filter students with exam results by exam year
            $studentsWithExamQuery = EnrolledStudent::has('results')
                ->where('exam_year', $year);
            $countWithExam = $studentsWithExamQuery->count();
    
            // Count total enrolled students for each exam year
            $totalEnrolledQuery = EnrolledStudent::where('exam_year', $year);
            $totalEnrolled = $totalEnrolledQuery->count();
    
            // Store the data for the current year
            $data[] = [
                'exam_year' => $year,
                'without_exam' => $countWithoutExam,
                'with_exam' => $countWithExam,
                'total_enrolled' => $totalEnrolled
            ];
        }
    
        // Return the data as JSON
        return response()->json($data);
    }
    
    

public function countStudentsbyCourse(Request $request){


     // Get the count of students for each exam_year
     $data = EnrolledStudent::select('course', \DB::raw('count(*) as count'))
     ->groupBy('course')
     ->orderBy('course') // optional, if you want to sort by year
     ->get();

return response()->json($data);
    
}

    
    
public function countEnrolledStudentsbyGender(Request $request)
{
    $examYear = $request->input('exam_year');
    $department = $request->input('department');  // Retrieve the department filter
    $course = $request->input('course');  // Retrieve the department filter

    // Query for counting male students, filtered by exam year and department if provided
    $maleCountQuery = EnrolledStudent::where('gender', 'M');
    if (!empty($examYear)) {
        $maleCountQuery->where('exam_year', $examYear);
    }
    if (!empty($department)) {
        $maleCountQuery->where('department', $department);
    }
    $maleCount = $maleCountQuery->count();

    // Query for counting female students, filtered by exam year and department if provided
    $femaleCountQuery = EnrolledStudent::where('gender', 'F');
    if (!empty($examYear)) {
        $femaleCountQuery->where('exam_year', $examYear);
    }
    if (!empty($department)) {
        $femaleCountQuery->where('department', $department);
    }
    $femaleCount = $femaleCountQuery->count();

    // Return the count as a JSON response
    return response()->json([
        'male_students_count' => $maleCount,
        'female_students_count' => $femaleCount
    ]);
}

    
    

   

 


   
   
}
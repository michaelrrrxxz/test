<?php

namespace App\Http\Controllers;

use App\Models\{EnrolledStudent, Batch, Result,ActivityLog,Instructor};
use Illuminate\Http\Request;
use App\Models\ExamAttempt;

use Illuminate\Support\Facades\Auth;
class InstructorDashboardController extends Controller
{
    public function getExamYearData()
{
    $department = Auth::user()->department;

    // Get the count of students for each exam_year, filtered by department
    $data = EnrolledStudent::where('department', $department)
                            ->select('exam_year', \DB::raw('count(*) as count'))
                            ->groupBy('exam_year')
                            ->orderBy('exam_year')
                            ->get();

    return response()->json($data);
}

public function getExamParticipationData(Request $request)
{
    $department = Auth::user()->department;
    $examYear = $request->input('exam_year');

    $studentsWithoutExamQuery = EnrolledStudent::where('department', $department)
                                               ->whereDoesntHave('results');
    if (!empty($examYear)) {
        $studentsWithoutExamQuery->where('exam_year', $examYear);
    }
    $countWithoutExam = $studentsWithoutExamQuery->count();

    $studentsWithExamQuery = EnrolledStudent::where('department', $department)
                                            ->has('results');
    if (!empty($examYear)) {
        $studentsWithExamQuery->where('exam_year', $examYear);
    }
    $countWithExam = $studentsWithExamQuery->count();

    $totalEnrolledQuery = EnrolledStudent::where('department', $department);
    if (!empty($examYear)) {
        $totalEnrolledQuery->where('exam_year', $examYear);
    }
    $totalEnrolled = $totalEnrolledQuery->count();

    return response()->json([
        'without_exam' => $countWithoutExam,
        'with_exam' => $countWithExam,
        'total_enrolled' => $totalEnrolled
    ]);
}
public function countEnrolledStudentsbyGender(Request $request)
{
    $department = Auth::user()->department;
    $examYear = $request->input('exam_year');

    $maleCountQuery = EnrolledStudent::where('department', $department)
                                     ->where('gender', 'M');
    if (!empty($examYear)) {
        $maleCountQuery->where('exam_year', $examYear);
    }
    $maleCount = $maleCountQuery->count();

    $femaleCountQuery = EnrolledStudent::where('department', $department)
                                       ->where('gender', 'F');
    if (!empty($examYear)) {
        $femaleCountQuery->where('exam_year', $examYear);
    }
    $femaleCount = $femaleCountQuery->count();

    return response()->json([
        'male_students_count' => $maleCount,
        'female_students_count' => $femaleCount
    ]);
}

public function getCategory(Request $request)
{
    // Get department and year from the request
    $department =  Auth::user()->department;
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


public function dashboard()
{
    $department = Auth::user()->department;

    $studentsWithoutExam = EnrolledStudent::where('department', $department)
                                          ->whereDoesntHave('results')
                                          ->select('id_number', 'name', 'course', 'department', 'gender')
                                          ->get();
    $countWithoutExam = $studentsWithoutExam->count();

    $countExam = Result::whereHas('enrolledStudent', function ($query) use ($department) {
                       $query->where('department', $department);
                   })->count();

    $enrolled = EnrolledStudent::where('department', $department)->count();

    return view('Instructors.Dashboard.index', compact(['enrolled', 'countWithoutExam', 'countExam']));
}


}
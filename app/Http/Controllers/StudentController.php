<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{
    Student,
    EnrolledStudent,
    Result,
    Course
};
use DB;

use Illuminate\Support\Carbon;


class StudentController extends Controller
{

    public function noExam(){
        $course = Course::get();
        
      
        return view ('Admin.Students.noexam',compact(['course']));
    }


    public function getnoExam()
    {
        
        $studentsWithoutExam = EnrolledStudent::whereDoesntHave('results')
            ->select('id_number', 'name', 'course', 'department','gender') 
            ->get();
    
        return response()->json(['data' => $studentsWithoutExam]);
    }
    
    

public function getStudents()
{
    $students = Student::with('batch')->get();
    return response()->json(['data' => $students]);
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.Students.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function assignAllToBatch($batchId)
    {
        // Find the batch
        $batch = Batch::find($batchId);
    
        if (!$batch) {
            return response()->json(['message' => 'Batch not found.'], 404);
        }
    
        // Count the number of students already in the batch
        $currentStudentCount = Student::where('batch_id', $batch->id)->count();
    
        // Check if the batch is already full
        if ($currentStudentCount >= 40) {
            return response()->json(['message' => 'Batch is already full. Maximum 40 students allowed.'], 400);
        }
    
        // Get all enrolled students who haven't been assigned to the students table yet
        $enrolledStudents = EnrolledStudent::all();
        
        // Loop through and assign each student to the batch until the batch is full
        $assignedCount = 0;
    
        foreach ($enrolledStudents as $enrolledStudent) {
            // Check if we can still add students to the batch
            if ($currentStudentCount + $assignedCount >= 40) {
                break; // Stop assigning once the batch is full
            }
    
            // Determine gender from male/female fields (assuming boolean values)
            $gender = $enrolledStudent->male ? 'male' : ($enrolledStudent->female ? 'female' : null);
    
            // Create the new student in the students table
            $student = Student::create([
                'id_number' => $enrolledStudent->id_number,
                'name' => $enrolledStudent->name,
                'course' => $enrolledStudent->course,
                'b_year' => 1, // Placeholder for birth year, adjust as needed
                'b_month' => 1, // Placeholder for birth month
                'b_date' => 1,  // Placeholder for birth date
                'ex_year' => $enrolledStudent->exam_year,
                'ex_month' => $enrolledStudent->exam_year,
                'ex_date' => $enrolledStudent->exam_year,
                'gender' =>  $enrolledStudent->gender,
                'batch_id' => $batch->id,
                'school' => 'your default school name', 
                'grade' => 1, 
                'age' => 1,  
                'm_age' => 1, 
                'tip' => 1,  
                'time_el' => 1, 
                'time' => 1, 
                'group_abc' => 1, 
            ]);
    
            // Optionally delete the enrolled student after transferring
            $enrolledStudent->delete();
    
            // Increment the count of assigned students
            $assignedCount++;
        }
    
        // Check if all students were assigned or if the batch reached its limit
        if ($assignedCount === 0) {
            return response()->json(['message' => 'No students assigned. Batch may already be full.'], 400);
        }
    
        return response()->json(['message' => "$assignedCount students assigned to batch successfully.", 'remaining_spots' => 40 - ($currentStudentCount + $assignedCount)], 200);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id)->delete();
        if($student){
            return response()->json(['result'=>'success','message'=>'The student has been deleted']);
         }else{
            return response()->json(['result'=>'error','message'=>'The student could not be deleted. Please try again!']);
         }
    }
}
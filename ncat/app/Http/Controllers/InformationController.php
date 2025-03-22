<?php

namespace App\Http\Controllers;
use App\Models\{Information, EnrolledStudent};

use Illuminate\Http\Request;
use Carbon\Carbon;

class InformationController extends Controller
{
    public function index(){
      return view ('Admin.Information.index')  ;
    }



    public function store(Request $request)
    {
        // Convert the 'birth_date' from MM/DD/YYYY to YYYY-MM-DD
        $birthDate = Carbon::createFromFormat('m/d/Y', $request->input('birth_date'))->format('Y-m-d');
    
        // Merge the converted birth_date back into the request data
        $data = $request->all();
        $data['birth_date'] = $birthDate;
    
        // Save the record
        $info = Information::create($data);
    
        // Return a success or error response
        if ($info) {
            // Assuming that 'student_id' exists in the Information model and
            // that you have a relationship to retrieve the student data
    
            // Retrieve the student details using the student_id
            $student = EnrolledStudent::find($info->student_id);  // Adjust if needed
    
            if ($student) {
                // Pass both the student_id and course to the route
                return redirect()->route('exam.test', [
                    'student_id' => $student->id,
                    'course' => $student->course  // Pass course from the student object
                ]);
            } else {
                return redirect()->back()->with('error', 'Student not found.');
            }
        } else {
            return response()->json(['result' => 'error', 'message' => 'The batch could not be saved. Please try again!']);
        }
    }
    
    
    
}
<?php

namespace App\Http\Controllers;
use App\Models\{Information, EnrolledStudent,Batch,Result,School};

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
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

       $data['school'] = School::firstOrCreate(
            ['school_name' => $request->input('school')]
        );


        // Save the record
        $info = Information::create($data);

        if ($info) {
            // Retrieve the student details using the student_id
            $student = EnrolledStudent::find($info->student_id);

            if ($student) {
                // Retrieve the active batch
                $activeBatch = Batch::where('status', 'active')->first();

                if (!$activeBatch) {
                    return redirect()->back()->with('error', 'No active batch found.');
                }

                // Create the result record
                Result::create([
                    'enrolled_student_id' => $student->id,
                    'batch_id' => $activeBatch->id,
                    'raw_score_t' => 0,
                    'test_ip' => $request->getClientIp(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Redirect to the test route with student_id and course
                return redirect()->route('exam.test', [
                    'student_id' => $student->id,
                    'course' => $student->course,
                ]);
            } else {
                return redirect()->back()->with('error', 'Student not found.');
            }
        } else {
            return response()->json(['result' => 'error', 'message' => 'The batch could not be saved. Please try again!']);
        }
    }







}

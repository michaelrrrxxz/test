<?php

namespace App\Http\Controllers;
use App\Models\{EnrolledStudent,Batch,Result,Information,Course,Department};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use Carbon\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class EnrolledStudentController extends Controller
{

    public function getEnrolledbyStudentsCourse(Request $request)
    {
        // Validate that the course parameter is present
        $request->validate([
            'course' => 'required|string',  // or 'required|integer' if you're using a course ID
        ]);

        // Retrieve the course parameter from the request
        $course = $request->input('course');

        // Fetch students enrolled in the specified course
        $students = EnrolledStudent::where('course', $course)->get();

        return response()->json(['data' => $students]);
    }


    public function getEnrolledStudents(Request $request)
    {   
        $year = $request->input('year');
        $query = EnrolledStudent::query();
    
        if ($year) {
            $query->where('exam_year', $year);
        }   
        $students = $query->get();
        return response()->json(['data' => $students]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::get();
        $departments = Department::get();
        
       
        return view('Admin.EnrolledStudents.index',compact(['course','departments']));
    }

    /**
     * Show the form for creating a new resource.
     */
    

     public function upload(Request $request)
     {
         $request->validate([
             'csv_file' => 'required|mimes:csv,txt',
         ]);
     
         $courseToDepartment = [
             'BSIT' => 'CIT',
             'BSN' => 'CON',
             'BSBA-FM' => 'CABA',
             'BSBA-MM' => 'CABA',
             'BSED-EU' => 'COED',
             'BSED-FIL' => 'COED',
             'BSED-MATH' => 'COED',
             'BSED-SCI' => 'COED',
             'BSA' => 'CABA',
             'BSED-ENGLISH' => 'COED',
             'BEED' => 'COED',
             'AB-MASS COM' => 'COLA',
             'BSCRIM' => 'COC',
             'BSHM' => 'COHM',
             'BSMA' => 'CABA',
             'AB-POL SCI' => 'COLA',
             'BSBA-HRM' => 'CABA',
             'MIDWIFERY' => 'SOM',
             'AB-ENG' => 'COLA',
             'BSGE' => 'COGE',
             'BSBA-MA' => 'CABA',
         ];
     
         function getDepartment($course, $courseToDepartment)
         {
             return isset($courseToDepartment[$course]) ? $courseToDepartment[$course] : 'Unknown Department';
         }
     
         $insertedCount = 0; // Initialize counter for inserted records
     
         if (($handle = fopen($request->file('csv_file'), 'r')) !== false) {
             fgetcsv($handle, 1000, ',');
     
             $studentsToInsert = [];
     
             while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                 $data = array_map(function ($value) {
                     return mb_convert_encoding($value, 'UTF-8', 'auto');
                 }, $data);
     
                 $id_number = $data[1];
                 $existingStudent = EnrolledStudent::where('id_number', $id_number)->first();
     
                 if (!$existingStudent) {
                     $course = $data[4];
     
                     $studentsToInsert[] = [
                         'id_number'  => $id_number,
                         'name'       => $data[2],
                         'course'     => $course,
                         'department' => getDepartment($course, $courseToDepartment),
                         'gender'     => $data[3],
                         'exam_year'  => date('Y'),
                     ];
                 }
             }
     
             if (!empty($studentsToInsert)) {
                 EnrolledStudent::insert($studentsToInsert);
                 $insertedCount = count($studentsToInsert); // Count the number of records inserted
             }
     
             fclose($handle);
         }
     
         return response()->json([
             'success' => 'Data imported successfully.',
             'inserted_count' => $insertedCount, // Include the count of inserted records
         ]);
     }
     

    
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'id_number' => 'required|unique:enrolled_students,id_number',
            'name' => 'required',
            'course' => 'required',
           
            'gender' => 'required|in:M,F', // Ensure only 'Male' or 'Female' are allowed
        ]);
    
        // Mapping of courses to departments
        $courseToDepartment = [
            'BSIT' => 'CIT',
            'BSN' => 'CON',
            'BSBA-FM' => 'CABA',
            'BSBA-MM' => 'CABA',
            'BSED-EU' => 'COED',
            'BSED-FIL' => 'COED',
            'BSED-MATH' => 'COED',
            'BSED-SCI' => 'COED',
            'BSA' => 'CABA',
            'BSED-ENGLISH' => 'COED',
            'BEED' => 'COED',
            'AB-MASS COM' => 'COLA',
            'BSCRIM' => 'COC',
            'BSHM' => 'COHM',
            'BSMA' => 'CABA',
            'AB-POL SCI' => 'COLA',
            'BSBA-HRM' => 'CABA',
            'MIDWIFERY' => 'SOM',
            'AB-ENG' => 'COLA',
            'BSGE' => 'COGE',
            'BSBA-MA' => 'CABA',
        ];
    
        // Helper function to get department based on course
        $getDepartment = function ($course) use ($courseToDepartment) {
            return $courseToDepartment[$course] ?? 'Unknown Department';
        };
    
        // Dynamically set the department based on the course
        $validatedData['department'] = $getDepartment($validatedData['course']);
    
        // Convert gender to 'M' or 'F'
      
    
        // Set the exam year to the current year
        $validatedData['exam_year'] = date('Y');
    
        // Attempt to create a new enrolled student record
        $enrolledStudent = EnrolledStudent::create($validatedData);
    
        // Respond based on the result of the creation
        if ($enrolledStudent) {
            return response()->json([
                'result' => 'success',
                'message' => 'The Student has been saved.',
            ]);
        } else {
            return response()->json([
                'result' => 'error',
                'message' => 'The Student could not be saved. Please try again!',
            ]);
        }
    }
    
   
   

    /**
     * Store a newly created resource in storage.
     */
    

    /**
     * Display the specified resource.
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $es = EnrolledStudent::find($id);
        return response()->json($es);
    }

    /**
     * Update the specified resource in storage.
     */



     public function update(Request $request, $id)
     {
         // Find the student by ID or fail with a 404 error
         $enrolledStudent = EnrolledStudent::findOrFail($id);
         dd($enrolledStudent);
     
         // Validate the request data
         $validatedData = $request->validate([
             'id_number' => [
                 'required',
                 Rule::unique('enrolled_students', 'id_number')->ignore($enrolledStudent->id),
             ],
             'name' => 'required',
             'course' => 'required',
             'department' => 'nullable', // Set as nullable since it will be auto-calculated
             'gender' => 'required|in:Male,Female',
         ]);
     
         // Mapping of courses to departments
         $courseToDepartment = [
             'BSIT' => 'CIT',
             'BSN' => 'CON',
             'BSBA-FM' => 'CABA',
             'BSBA-MM' => 'CABA',
             'BSED-EU' => 'COED',
             'BSED-FIL' => 'COED',
             'BSED-MATH' => 'COED',
             'BSED-SCI' => 'COED',
             'BSA' => 'CABA',
             'BSED-ENGLISH' => 'COED',
             'BEED' => 'COED',
             'AB-MASS COM' => 'COLA',
             'BSCRIM' => 'COC',
             'BSHM' => 'COHM',
             'BSMA' => 'CABA',
             'AB-POL SCI' => 'COLA',
             'BSBA-HRM' => 'CABA',
             'MIDWIFERY' => 'SOM',
             'AB-ENG' => 'COLA',
             'BSGE' => 'COGE',
             'BSBA-MA' => 'CABA',
         ];
     
         // Helper function to get the department based on the course
         $getDepartment = function ($course) use ($courseToDepartment) {
             return $courseToDepartment[$course] ?? 'Unknown Department';
         };
     
         // Dynamically assign the department based on the course
         $validatedData['department'] = $getDepartment($validatedData['course']);
     
         // Convert gender to 'M' or 'F'
         $genderMapping = [
             'Male' => 'M',
             'Female' => 'F',
         ];
         $validatedData['gender'] = $genderMapping[$validatedData['gender']] ?? $validatedData['gender'];
     
         // Update the record
         $updated = $enrolledStudent->update($validatedData);
     
         // Return a JSON response
         if ($updated) {
             return response()->json([
                 'result' => 'success',
                 'message' => 'The Student record has been updated.',
             ]);
         } else {
             return response()->json([
                 'result' => 'error',
                 'message' => 'The Student record could not be updated. Please try again!',
             ]);
         }
     }
     




public function generatePdf()
{
    // Fetch data for your table; replace `Student` with your model
    $students = EnrolledStudent::select(['id_number', 'course', 'name', 'gender'])->get();

    // Pass the data to a dedicated PDF view
    $pdf = SnappyPdf::loadView('Admin.Students.no_exam_students', ['students' => $students]);

    // Return the PDF inline to view in the browser or download
    return $pdf->inline('Admin.Students.noexam');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = EnrolledStudent::find($id)->delete();
        if($student){
            return response()->json(['result'=>'success','message'=>'The user has been deleted']);
         }else{
            return response()->json(['result'=>'error','message'=>'The user could not be deleted. Please try again!']);
         }
    }


    public function sendOtp(Request $request)
    {
       
        $request->validate([
            'id_number' => 'required',
            'email' => 'required|email',
        ]);
    
      
        $student = EnrolledStudent::where('id_number', $request->input('id_number'))->first();


        
    
        if ($student) {
          
            $resultExists = DB::table('results')
            ->where('enrolled_student_id', $student->id) // Assuming 'id' is the primary key of EnrolledStudent
            ->exists();

        if ($resultExists){
            return redirect()->back()->with('error', 'ID Already Exist.');
        } else {
            $year = date('Y');  
            // $randomNumber = mt_rand(10000000, 99999999); 
            // $otp = $year . $randomNumber;

            $otp = $year;
    
            try {
                // Send OTP email
                // Mail::to($request->input('email'))->send(new OtpMail($otp));     
    
                // Store the OTP in the session (you can also store it in the database if you prefer)
                session([
                    'otp' => $otp,
                    'id_number' => $request->input('id_number'),
                    'email' => $request->input('email')
                ]);
    
                // Redirect to the verify OTP page
                return redirect()->route('verify.otp')->with('success', 'OTP has been sent to your email.');
            } catch (\Exception $e) {
                // Handle errors with sending mail (such as SMTP issues)
                return redirect()->back()->with('error', 'Failed to send OTP. Please try again later.');
            }
        }
        } else {
            // If id_number does not exist, return an error message
            return redirect()->back()->with('error', 'ID number not found in enrolled students.');
        }
    }

    public function verifyOtp(Request $request)
    {
        // Validate the OTP input
        $request->validate([
            'otp' => 'required|numeric',
        ]);
    
        $storedOtp = session('otp');
        $otpCreatedAt = session('otp_created_at');
        $idNumber = session('id_number');
    
        // Check if OTP is older than 5 minutes
        if (now()->diffInMinutes($otpCreatedAt) > 5) {
            return redirect()->back()->with('error', 'OTP has expired. Please request a new one.');
        }
    
        // Check if the entered OTP matches the stored OTP
        if ($request->input('otp') == $storedOtp) {
            // OTP is correct, proceed with further logic (e.g., login, access grant)
            // Clear the session OTP to prevent reuse
            session()->forget('otp');
            session()->forget('otp_created_at');
    
            // Get the student record from the enrolledstudent table using id_number
            $student = EnrolledStudent::where('id_number', $idNumber)->first();
      
    
            // Get the active batch from the batch table
            $activeBatch = Batch::where('status', 'active')->first();
         
            if ($student && $activeBatch) {
                // Insert data into the results table
                Result::create([
                    'enrolled_student_id' => $student->id,
                    'batch_id' => $activeBatch->id,
                    'raw_score_t' => 0,
                    'test_ip' => request()->getClientIp(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            
                // Redirect and pass student details, including the course
                return redirect('Exam/information-form')->with([
                    'studentId' => $student->id,
                    'studentName' => $student->name,  // Use 'name' for student's name
                    'course' => $student->course,     // Access the 'course' directly from student object
                ]);
            }
            
             else {
                // If student or batch doesn't exist, return an error
                return redirect()->back()->with('error', 'Failed to find student or active batch.');
            }
        } else {
            // OTP is incorrect, return an error
            return redirect()->back()->with('error', 'Invalid OTP. Please try again.');
        }
    }
    


 
        // Method to fetch all students along with their results and information
        // public function getStudentData()
        // {
        //     // Fetch all information entries with related student and result
        //     $information = Information::with(['students.results'])->get();
    
        //     // Check if any information exists
        //     if ($information->isEmpty()) {
        //         return response()->json(['error' => 'No information found'], 404);
        //     }
    
        //     // Return all information, students, and results as JSON
        //     return response()->json(['data' => $information]);
           
        // }
      

        public function getStudentData()
        {
            $information = Information::with(['students', 'region', 'city', 'province', 'school', 'students.results.batch'])->get()->map(function ($info) {
                $birthday = $info->students->birth_date ?? null;
        
                return [
                    'id' => $info->id,
                    'student_id' => $info->student_id,
                    'birth_date' => $info->birth_date,
                    'address' => implode(", ", array_filter([
                        $info->region->name ?? null,  // Combining region
                        $info->city->name ?? null,    // Combining city
                        $info->province->name ?? null, // Combining province
                    ])),
                    'school' => $info->school->school_name ?? null, // Assuming 'school_name' exists in School model
                    'students' => [
                        'id_number' => $info->students->id_number ?? null,
                        'name' => $info->students->name ?? null,
                        'course' => $info->students->course ?? null,
                        'department' => $info->students->department ?? null,
                        'address' => $info->students->address ?? null,
                        'gender' => $info->students->gender ?? null,
                        'birthday' => $birthday,
                        'age' => $birthday ? Carbon::parse($birthday)->age : null, // Age calculation
                        'exam_year' => $info->students->exam_year ?? null,
                        'results' => $info->students->results->map(function ($result) {
                            return [
                                'batch' => $result->batch->name ?? null, 
                                'raw_score_t' => $result->raw_score_t,
                                'test_ip' => $result->test_ip,
                            ];
                        }),
                    ],
                    'created_at' => $info->created_at,
                    'updated_at' => $info->updated_at,
                ];
            });
        
            return response()->json(['data' => $information]);
        }
        

        public function verifyAccessKey(Request $request)
        {
            // Validate the request input
            $request->validate([
                'id_number' => 'required|string',
                'access_key' => 'required|string',
            ]);
        
            // Retrieve the student by id_number
            $student = EnrolledStudent::where('id_number', $request->input('id_number'))->first();
        
            if (!$student) {
                return redirect()->back()->with('error', 'Student not found.');
            }
        
            // Retrieve the active batch from the batch table
            $activeBatch = Batch::where('status', 'active')->first();
        
            if (!$activeBatch) {
                return redirect()->back()->with('error', 'No active batch found.');
            }
        
            // Trim and convert both keys to lowercase for comparison
            $submittedKey = strtolower(trim($request->input('access_key')));
            $storedKey = strtolower(trim($activeBatch->access_key));
        
            if ($submittedKey !== $storedKey) {
                return redirect()->back()->with('error', 'Invalid Access Key. Please try again.');
            }
        
            // Redirect to the information form with student data
            return redirect('Exam/information-form')->with([
                'studentId' => $student->id,
                'studentName' => $student->name,
                'course' => $student->course,
            ]);
        }
        
          
        
        
        
 
        public function NoEmail(){
            return view('root.active-index-no-email');
        }
    
}
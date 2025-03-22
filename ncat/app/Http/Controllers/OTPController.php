<?php

namespace App\Http\Controllers;
use App\Models\{EnrolledStudent,Batch,Result,Information};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;


use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OTPController extends Controller
{
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
           
         
            $otp =    mt_rand(100000, 999999); 



            // dd($otp);
            // $otp = $year;
    
            try {
                // Send OTP email
                Mail::to($request->input('email'))->send(new OtpMail($otp));     
    
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
    

}
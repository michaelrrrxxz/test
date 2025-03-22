<?php

namespace App\Http\Controllers;
use App\Models\{Instructor,User,ExamAttempt,EnrolledStudent,Result,Batch};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class InstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     */

    public function getInstructors(){
        $instructor = Instructor::all();
        return response()->json(['data' => $instructor]);
    }
     
    public function index()
    {
        return view('Admin.Instructors.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
      
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|numeric|unique:instructors,id_number',
            'fullname' => 'required',
            'username' => 'required|string|min:4|unique:instructors,username',
            'department' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\W).*$/'
            ],
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one special character.',
                'password.min' => 'The password must be at least 8 characters long.'
           
          
             
        ]);
    
        // If validation fails, return the error response
        if ($validator->fails()) {
            return response()->json([
                'result' => 'error',
                'message' => $validator->errors()->first()
            ]);
        }
    
        // Hash the password before saving
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
    
        // Create the instructor
        $instructor = Instructor::create($data);
    
        // Return response based on success or failure
        if ($instructor) {
            return response()->json(['result' => 'success', 'message' => 'The instructor has been saved']);
        } else {
            return response()->json(['result' => 'error', 'message' => 'The instructor could not be saved. Please try again!']);
        }
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
       $instructor = Instructor::find($id);
        return response()->json($instructor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $instructor = Instructor::find($id);
        $instructor = $instructor->update($request->all());
        if($instructor){
            return response()->json(['result'=>'success','message'=>'The instructor has been saved']);
         }else{
            return response()->json(['result'=>'error','message'=>'The instructor could not be saved. Please try again!']);
         }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $instructor = Instructor::find($id)->delete();
        if($instructor){
            return response()->json(['result'=>'success','message'=>'The instructor has been deleted']);
         }else{
            return response()->json(['result'=>'error','message'=>'The instructor could not be deleted. Please try again!']);
         }
    }
}
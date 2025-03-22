<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Department;
class CourseController extends Controller
{
    public function getCourses()
    {
        $courses = Course::with('department')->get();
    
        $coursesData = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'name' => $course->name,
                'department_name' => $course->department ? $course->department->name : null, // Get department name or null
            ];
        });
    
        return response()->json(['data' => $coursesData]);
    }
    
public function index(){
    $departments = Department::all();
    return view('Admin.Courses.index',compact(['departments']));
}

public function store(Request $request)
{

  
    // Validate the request data, including file upload and conditional department field
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|unique:courses,name',
        
    ]);

    if ($validator->fails()) {
        return response()->json([
            'result' => 'error',
            'message' => $validator->errors()->first(), // First error message
            'errors' => $validator->errors() // All validation errors
        ]);
    }


    $data =  $request->all();
    
    $user = Course::create($data);



    return response()->json([
        'result' => $user ? 'success' : 'error',
        'message' => $user ? 'The course has been saved' : 'The user could not be saved. Please try again!'
    ]);
}

public function edit(string $id)
{
   $course = Course::find($id);
    return response()->json($course);
}

public function update(Request $request, string $id)
{
    $course = Course::find($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        
    ]);

    if (!$course) {
        return response()->json(['result' => 'error', 'message' => 'User not found.']);
    }
    
    $update = $course->update($request->all());
    if($update){
        return response()->json(['result'=>'success','message'=>'The course has been saved']);
     }else{
        return response()->json(['result'=>'error','message'=>'The course could not be saved. Please try again!']);
     }
}

public function destroy(string $id)
{
    $course = Course::find($id)->delete();
    if($course){
        return response()->json(['result'=>'success','message'=>'The course has been deleted']);
     }else{
        return response()->json(['result'=>'error','message'=>'The course could not be deleted. Please try again!']);
     }
}
  



}
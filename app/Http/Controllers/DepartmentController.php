<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Department;
class DepartmentController extends Controller
{
    public function getDepartments()
{
    $department = Department::get();
    return response()->json(['data' => $department]);
}

public function index(){
    return view('Admin.Departments.index');
}

public function store(Request $request)
{

  
    // Validate the request data, including file upload and conditional department field
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|unique:courses,name',
        
    ]);

    // If validation fails, return error response
    if ($validator->fails()) {
        return response()->json([
            'result' => 'error',
            'message' => $validator->errors()->first()
        ]);
    }


    $data =  $request->all();
    
    $dept = Department::create($data);



    return response()->json([
        'result' => $dept ? 'success' : 'error',
        'message' => $dept ? 'The Department has been saved' : 'The user Department not be saved. Please try again!'
    ]);
}

public function edit(string $id)
{
   $dept = Department::find($id);
    return response()->json($dept);
}

public function update(Request $request, string $id)
{
    $dept = Department::find($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        
    ]);

    if (!$dept) {
        return response()->json(['result' => 'error', 'message' => 'User not found.']);
    }
    
    $update = $dept->update($request->all());
    if($update){
        return response()->json(['result'=>'success','message'=>'The Department has been saved']);
     }else{
        return response()->json(['result'=>'error','message'=>'The Department could not be saved. Please try again!']);
     }
}

public function destroy(string $id)
{
    $dept = Department::find($id)->delete();
    if($dept){
        return response()->json(['result'=>'success','message'=>'The Department has been deleted']);
     }else{
        return response()->json(['result'=>'error','message'=>'The Department could not be deleted. Please try again!']);
     }
}
  



}
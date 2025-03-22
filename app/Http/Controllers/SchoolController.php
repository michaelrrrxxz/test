<?php

namespace App\Http\Controllers;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class SchoolController extends Controller
{

  public function getSchools(Request $request)
  {
    
      $schools = School::with('city.province.region')->get();

 
      $data = $schools->map(function ($school) {
          return [
              'id'=> $school->id,
              'school_name' => $school->school_name,
              'city' => $school->city ? $school->city->name : 'N/A',
              'province' => $school->city && $school->city->province ? $school->city->province->name : 'N/A',
              'region' => $school->city && $school->city->province && $school->city->province->region ? $school->city->province->region->name : 'N/A',
          ];
      });

      return response()->json(['data' => $data]);
  }
      public function index()
        {
          return view('Admin.Schools.index');
        }

    public function store(Request $request)
    {
        $school = School::create($request->all());
        if($school){
          return response()->json(['result'=>'success','message'=>'The school has been saved']);
        }else{
          return response()->json(['result'=>'error','message'=>'The school could not be saved. Please try again!']);
        }
    }
    public function update(Request $request, string $id)
    {
        $s = School::find($id);
        $s = $s->update($request->all());
        if($s){
            return response()->json(['result'=>'success','message'=>'The instructor has been saved']);
         }else{
            return response()->json(['result'=>'error','message'=>'The instructor could not be saved. Please try again!']);
         }

    }

  public function destroy(string $id)
  {
      $school = School::find($id)->delete();
      if($school){
          return response()->json(['result'=>'success','message'=>'The school has been deleted']);
       }else{
          return response()->json(['result'=>'error','message'=>'The school could not be deleted. Please try again!']);
       }
  }


  public function getSchoolsname (): JsonResponse
  {
    $schools = School::with('city.province.region')->get();

 
    $school = $schools->map(function ($school) {
        return [
            'id'=> $school->id,
            'school_name' => $school->school_name,
            'city' => $school->city ? $school->city->name : 'N/A',
            'province' => $school->city && $school->city->province ? $school->city->province->name : 'N/A',
            'region' => $school->city && $school->city->province && $school->city->province->region ? $school->city->province->region->name : 'N/A',
        ];
    });
      return response()->json($school);
  }
  
}